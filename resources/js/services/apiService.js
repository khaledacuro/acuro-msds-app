// src/services/apiService.js

import axios from "axios";

const BASE_URL = "http://localhost:8000/api";

// Fetch paginated documents
export async function fetchDocuments(url = `${BASE_URL}/documents`, params = {}) {
  const response = await axios.get(url, { params });
  return response.data;
}

// Fetch all documents for export
export async function exportDocuments(params = {}) {
  const response = await axios.get(`${BASE_URL}/documents/export`, { params });
  return response.data;
}



// Fetch a single document by ID
export async function fetchDocumentById(id) {
  const response = await axios.get(`${BASE_URL}/documents/${id}`)
  return response.data
}

// Create a new document
export async function createDocument(documentData) {
  const response = await axios.post(`${BASE_URL}/documents`, documentData)
  return response.data
}

// Update an existing document
export async function updateDocument(id, documentData) {
  const response = await axios.put(`${BASE_URL}/documents/${id}`, documentData)
  return response.data
}

// Delete a document
export async function deleteDocument(id) {
  const response = await axios.delete(`${BASE_URL}/documents/${id}`)
  return response.data
}