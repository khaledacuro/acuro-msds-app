// src/services/apiService.js

import axios from 'axios'

const BASE_URL = 'http://localhost:8000'

// Fetch all documents
export async function fetchDocuments() {
  const response = await axios.get(`${BASE_URL}/api/documents`)
  return response.data
}

// Fetch a single document by ID
export async function fetchDocumentById(id) {
  const response = await axios.get(`${BASE_URL}/api/documents/${id}`)
  return response.data
}

// Create a new document
export async function createDocument(documentData) {
  const response = await axios.post(`${BASE_URL}/api/documents`, documentData)
  return response.data
}

// Update an existing document
export async function updateDocument(id, documentData) {
  const response = await axios.put(`${BASE_URL}/api/documents/${id}`, documentData)
  return response.data
}

// Delete a document
export async function deleteDocument(id) {
  const response = await axios.delete(`${BASE_URL}/api/documents/${id}`)
  return response.data
}