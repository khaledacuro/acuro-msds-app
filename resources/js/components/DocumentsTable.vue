<template>
  <div class="overflow-x-auto">
    <div class="table-container">
      <div class="mb-4 flex">
        <div class="mr-4">
          <label for="startDate">Start Date:</label>
          <date-picker
            id="startDate"
            v-model="startDate"
            type="datetime"
            valueType="format"
            class="mr-4 border border-gray-300 rounded px-2 py-1 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
          />
        </div>
        <div>
          <label for="endDate">End Date:</label>
          <date-picker
            id="endDate"
            v-model="endDate"
            type="datetime"
            valueType="format"
            class="border border-gray-300 rounded px-2 py-1 focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
          />
        </div>
      </div>
      <button @click="exportToExcel" class="btn text-white px-4 py-2 mb-4">Export Selected</button>
      <table id="myTable" class="min-w-full bg-white border shadow-lg pt-2 pb-2">
        <thead class="bg-primary text-white sticky-header">
          <tr class="text-left">
            <th class="py-3 px-6 border-b">
              <input type="checkbox" @change="toggleSelectAll" v-model="selectAll" />
            </th>
            <th class="py-3 px-6 border-b">File Name</th>
            <!-- Add created_at and updated_at column headers -->
            <th class="py-3 px-6 border-b">Created</th>
            <th class="py-3 px-6 border-b">Updated</th>
            <th v-for="(field, index) in fieldNames" :key="index" class="py-2 px-4 border-b">
              {{ field }}
            </th>

            <th class="py-3 px-6 border-b">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(doc, docIndex) in filteredDocuments"
            :key="docIndex"
            class="hover:bg-gray-100"
          >
            <td class="py-3 px-6 border-b">
              <input type="checkbox" v-model="selectedDocuments" :value="doc" />
            </td>
            <td class="py-3 px-6 border-b">{{ doc.file_name }}</td>
            <td class="py-3 px-6 border-b">{{ doc.created_at }}</td>
            <td class="py-3 px-6 border-b">{{ doc.updated_at }}</td>
            <td v-for="(field, index) in fieldNames" :key="index" class="py-3 px-6 border-b">
              {{ getFieldData(doc, field)?.value || 'N/A' }} <br />
              <span
                :class="
                  getFieldData(doc, field)?.confidence >= 85 ? 'text-secondary' : 'text-error'
                "
                >{{ getFieldData(doc, field)?.confidence }}%</span
              >
            </td>
            <td class="py-2 px-4 border-b">
              <router-link :to="{ name: 'DocumentDetail', params: { id: doc.id } }">
                <button class="btn text-white px-4 py-2 rounded">View</button>
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
import DatePicker from 'vue3-datepicker'
import * as XLSX from 'xlsx'
import $ from 'jquery'
import 'datatables.net-dt/css/dataTables.dataTables.min.css'
import 'datatables.net'

export default {
  name: 'DocumentsTable',
  components: {
    DatePicker
  },
  props: {
    documents: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      selectedDocuments: [],
      selectAll: false,
      startDate: null,
      endDate: null,
      filteredDocuments: []
    }
  },
  computed: {
    fieldNames() {
      console.log('Calculating field names from documents')
      const fieldNamesSet = new Set()
      this.documents.forEach((doc) =>
        doc.document_data.forEach((fieldData) => fieldNamesSet.add(fieldData.field.field_name))
      )
      // console.log('Field names:', Array.from(fieldNamesSet))
      return Array.from(fieldNamesSet)
    }
  },
  methods: {
    getFieldData(doc, fieldName) {
      // console.log('Getting field data for document:', doc, 'and field:', fieldName)
      return doc.document_data.find((item) => item.field.field_name === fieldName) || {}
    },
    toggleSelectAll() {
      // console.log('Toggling select all. Current state:', this.selectAll)
      this.selectedDocuments = this.selectAll ? [...this.filteredDocuments] : []
    },
    filterDocuments() {
      console.log('Filtering documents from:', this.startDate, 'to:', this.endDate)
      if (!this.startDate || !this.endDate) {
        this.filteredDocuments = this.documents
        console.log('Filtered documents (no date range):', this.filteredDocuments)
        return
      }
      const start = new Date(this.startDate).getTime()
      const end = new Date(this.endDate)
      end.setHours(23, 59, 59, 999)
      this.filteredDocuments = this.documents.filter((doc) => {
        const createdAt = new Date(doc.created_at).getTime()
        return createdAt >= start && createdAt <= end.getTime()
      })
      // console.log('Filtered documents:', this.filteredDocuments)
    },
    exportToExcel() {
      console.log('Exporting selected documents to Excel:', this.selectedDocuments)
      if (this.selectedDocuments.length === 0) {
        alert('Please select at least one row to export.')
        return
      }
      const dataToExport = this.selectedDocuments.map((doc) => {
        const rowData = {
          File_Name: doc.file_name,
          Created_at: doc.created_at,
          Updated_at: doc.updated_at
        }
        this.fieldNames.forEach((field) => {
          const fieldData = this.getFieldData(doc, field)
          rowData[field] = fieldData.value || 'N/A'
          rowData[`${field} confidence`] =
            fieldData.confidence !== undefined ? fieldData.confidence : 'N/A'
        })
        return rowData
      })
      console.log('Data to export:', dataToExport)
      const worksheet = XLSX.utils.json_to_sheet(dataToExport)
      const workbook = XLSX.utils.book_new()
      XLSX.utils.book_append_sheet(workbook, worksheet, 'MSDS-Data')
      //styling
      const range = XLSX.utils.decode_range(worksheet['!ref'])

      worksheet['!cols'] = []
      for (let C = range.s.c; C <= range.e.c; ++C) {
        let maxWidth = 10 // Minimum column width
        for (let R = range.s.r; R <= range.e.r; ++R) {
          const cell = worksheet[XLSX.utils.encode_cell({ r: R, c: C })]
          if (cell && cell.v) {
            const cellLength = cell.v.toString().length
            if (cellLength > maxWidth) maxWidth = cellLength
          }
        }
        worksheet['!cols'][C] = { wch: maxWidth + 2 } // Add padding
      }
      // Write to file
      XLSX.writeFile(workbook, 'msdsdata.xlsx')
    },
    initializeDataTable() {
      console.log('Initializing DataTable')
      $('#myTable').DataTable({
        // paging: true,
        paging: false,
        searching: true,
        ordering: true,
        responsive: true,
        // lengthChange: true
        lengthChange: false

      })
    },
    reinitializeDataTable() {
      console.log('Reinitializing DataTable')
      const table = $('#myTable').DataTable()
      if (table) {
        table.clear().destroy()
      }
      this.initializeDataTable()
    }
  },
  mounted() {
    console.log('Mounted DocumentsTable')
    this.filteredDocuments = this.documents
    this.$nextTick(() => {
      this.initializeDataTable()
    })
  },
  watch: {
    startDate() {
      console.log('Start date changed:', this.startDate)
      this.filterDocuments()
    },
    endDate() {
      console.log('End date changed:', this.endDate)
      this.filterDocuments()
    },
    documents() {
      console.log('Documents prop changed:', this.documents)
      this.filteredDocuments = this.documents
      this.$nextTick(() => {
        this.reinitializeDataTable()
      })
    }
  }
}
</script>

<style scoped>
.table-container {
  max-height: 70vh; /* Table's max height will be 70% of the viewport height */
  overflow-y: auto; /* Enable vertical scroll */
}

.sticky-header th {
  position: sticky;
  top: 0;
  z-index: 1;
  background-color: var(--primary-color);
}
</style>