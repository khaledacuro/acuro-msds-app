<template>
  <div class="overflow-x-auto">
    <div class="table-container">
      <!-- Search and Date Filters -->
      <div class="mb-4 flex">

        <div class="mr-4">
          <label for="searchQuery">Search by Trade Name:</label>
          <br>
          <input id="searchQuery" type="text" v-model="searchQuery" @input="fetchPaginatedData"
            placeholder="Enter Trade Name..."
            class="border border-gray-300 rounded px-2 py-1 focus:ring-2 focus:ring-blue-200" />
        </div>


        <div class="mr-4">
          <label for="startDate">Start Date:</label>
          <date-picker id="startDate" v-model="startDate" @change="fetchPaginatedData" />
        </div>

        <div>
          <label for="endDate">End Date:</label>
          <date-picker id="endDate" v-model="endDate" @change="fetchPaginatedData" />
        </div>

      </div>

      <!-- Export Button -->
      <button @click="exportToExcel" class="btn text-white px-4 py-2 mb-4">Export Selected</button>

      <!-- Documents Table -->
      <table class="min-w-full bg-white border shadow-lg">
        <thead class="bg-primary text-white sticky-header">
          <tr>
            <th class="py-3 px-6 border-b">
              <!-- <input type="checkbox" @change="toggleSelectAll" v-model="selectAll" /> -->
            </th>
            <th @click="sortBy('file_name')" class="py-3 px-6 border-b cursor-pointer">File Name</th>
            <th @click="sortBy('created_at')" class="py-3 px-6 border-b cursor-pointer">Created</th>
            <th @click="sortBy('updated_at')" class="py-3 px-6 border-b cursor-pointer">Updated</th>
            <th v-for="(field, index) in fieldNames" :key="index" class="py-3 px-6 border-b">
              {{ field }}
            </th>
            <th class="py-3 px-6 border-b">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(doc, index) in paginatedDocuments" :key="index" class="hover:bg-gray-100">
            <td class="py-3 px-6 border-b">
              <input type="checkbox" v-model="selectedDocuments" :value="doc" />
            </td>
            <td class="py-3 px-6 border-b">{{ doc.file_name }}</td>
            <td class="py-3 px-6 border-b">{{ doc.created_at }}</td>
            <td class="py-3 px-6 border-b">{{ doc.updated_at }}</td>
            <td v-for="(field, index) in fieldNames" :key="index" class="py-3 px-6 border-b">
              {{ getFieldData(doc, field)?.value || "N/A" }}
            </td>
            <td class="py-3 px-6 border-b">
              <router-link :to="{ name: 'DocumentDetail', params: { id: doc.id } }">
                <button class="btn text-white px-4 py-2">View</button>
              </router-link>
            </td>
          </tr>
          <tr v-if="paginatedDocuments.length === 0">
            <td colspan="100%" class="text-center py-3">No documents found.</td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination Links -->
      <div class="mt-4 flex justify-between">
        <button @click="fetchPaginatedData(pagination.prev_page_url)" :disabled="!pagination.prev_page_url"
          class="btn text-white px-4 py-2">Previous</button>
        <button @click="fetchPaginatedData(pagination.next_page_url)" :disabled="!pagination.next_page_url"
          class="btn text-white px-4 py-2">Next</button>
      </div>
    </div>
  </div>
</template>

<script>
import DatePicker from "vue3-datepicker";
import * as XLSX from "xlsx";
import { fetchDocuments } from "../services/apiService";

export default {
  components: { DatePicker },
  data() {
    return {
      paginatedDocuments: [],
      fieldNames: [],
      selectedDocuments: [],
      selectAll: false,
      searchQuery: "",
      startDate: null,
      endDate: null,
      pagination: {},
      sortColumn: "created_at",
      sortDirection: "asc",
    };
  },
  methods: {
    fetchPaginatedData(url = null) {
      const fetchUrl = url || "http://localhost:8000/api/documents";
      const params = {
        search: this.searchQuery,
        start_date: this.startDate,
        end_date: this.endDate,
        sort_column: this.sortColumn,
        sort_direction: this.sortDirection,
      };

      fetchDocuments(fetchUrl, params).then((response) => {
        this.paginatedDocuments = response.data;
        this.pagination = {
          current_page: response.current_page,
          last_page: response.last_page,
          next_page_url: response.next_page_url,
          prev_page_url: response.prev_page_url,
        };
        this.fieldNames = this.extractFieldNames(response.data);
      });
    },
    sortBy(column) {
      this.sortDirection = this.sortColumn === column && this.sortDirection === "asc" ? "desc" : "asc";
      this.sortColumn = column;
      this.fetchPaginatedData();
    },
    // toggleSelectAll() {
    //   this.selectedDocuments = this.selectAll ? [...this.paginatedDocuments] : [];
    // },
    getFieldData(doc, fieldName) {
      return doc.document_data.find((item) => item.field.field_name === fieldName) || {};
    },
    extractFieldNames(data) {
      const fieldNamesSet = new Set();
      data.forEach((doc) =>
        doc.document_data.forEach((fieldData) => fieldNamesSet.add(fieldData.field.field_name))
      );
      return Array.from(fieldNamesSet);
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
  },
  watch: {
    searchQuery() {
      this.fetchPaginatedData();
    },
    startDate() {
      this.fetchPaginatedData();
    },
    endDate() {
      this.fetchPaginatedData();
    },
  },
  mounted() {
    this.fetchPaginatedData();
  },
};
</script>

<style scoped>
.table-container {
  max-height: 70vh;
  overflow-y: auto;
}

.sticky-header th {
  position: sticky;
  top: 0;
  z-index: 1;
  background-color: var(--primary-color);
}

.btn:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}
</style>