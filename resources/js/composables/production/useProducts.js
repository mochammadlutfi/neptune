import { ref, computed } from 'vue'
import { ProductService } from '@/Services'

/**
 * Product Composable
 * Provides reactive product data and operations
 */
export function useProducts() {
    // Reactive state
    const products = ref([])
    const product = ref(null)
    const loading = ref(false)
    const error = ref(null)
    const pagination = ref({
        current_page: 1,
        per_page: 15,
        total: 0,
        last_page: 1
    })

    // Computed properties
    const hasProducts = computed(() => products.value.length > 0)
    const hasError = computed(() => error.value !== null)
    const isLoading = computed(() => loading.value)

    // Methods
    const clearError = () => {
        error.value = null
    }

    const setLoading = (value) => {
        loading.value = value
    }

    const handleError = (err) => {
        error.value = err
        console.error('Product operation error:', err)
    }

    // Fetch products with pagination and filters
    const fetchProducts = async (params = {}) => {
        try {
            clearError()
            setLoading(true)
            
            const response = await ProductService.getAll(params)
            
            products.value = response.data
            pagination.value = {
                current_page: response.current_page,
                per_page: response.per_page,
                total: response.total,
                last_page: response.last_page
            }
            
            return response
        } catch (err) {
            handleError(err)
            throw err
        } finally {
            setLoading(false)
        }
    }

    // Fetch single product
    const fetchProduct = async (id, params = {}) => {
        try {
            clearError()
            setLoading(true)
            
            const response = await ProductService.getById(id, params)
            product.value = response.data
            
            return response
        } catch (err) {
            handleError(err)
            throw err
        } finally {
            setLoading(false)
        }
    }

    // Create product
    const createProduct = async (data) => {
        try {
            clearError()
            setLoading(true)
            
            const response = await ProductService.create(data)
            
            // Add to products list if it exists
            if (hasProducts.value) {
                products.value.unshift(response.data)
            }
            
            return response
        } catch (err) {
            handleError(err)
            throw err
        } finally {
            setLoading(false)
        }
    }

    // Update product
    const updateProduct = async (id, data) => {
        try {
            clearError()
            setLoading(true)
            
            const response = await ProductService.update(id, data)
            
            // Update in products list if it exists
            const index = products.value.findIndex(p => p.id === id)
            if (index !== -1) {
                products.value[index] = response.data
            }
            
            // Update current product if it matches
            if (product.value && product.value.id === id) {
                product.value = response.data
            }
            
            return response
        } catch (err) {
            handleError(err)
            throw err
        } finally {
            setLoading(false)
        }
    }

    // Delete product
    const deleteProduct = async (id) => {
        try {
            clearError()
            setLoading(true)
            
            const response = await ProductService.remove(id)
            
            // Remove from products list
            const index = products.value.findIndex(p => p.id === id)
            if (index !== -1) {
                products.value.splice(index, 1)
            }
            
            // Clear current product if it matches
            if (product.value && product.value.id === id) {
                product.value = null
            }
            
            return response
        } catch (err) {
            handleError(err)
            throw err
        } finally {
            setLoading(false)
        }
    }

    // Search products
    const searchProducts = async (query, params = {}) => {
        try {
            clearError()
            setLoading(true)
            
            const response = await ProductService.search(query, params)
            products.value = response.data
            
            return response
        } catch (err) {
            handleError(err)
            throw err
        } finally {
            setLoading(false)
        }
    }

    // Get product variants
    const fetchProductVariants = async (productId, params = {}) => {
        try {
            clearError()
            
            const response = await ProductService.getVariants(productId, params)
            return response
        } catch (err) {
            handleError(err)
            throw err
        }
    }

    // Get product stock
    const fetchProductStock = async (productId, params = {}) => {
        try {
            clearError()
            
            const response = await ProductService.getStock(productId, params)
            return response
        } catch (err) {
            handleError(err)
            throw err
        }
    }

    // Upload product images
    const uploadProductImages = async (productId, files) => {
        try {
            clearError()
            setLoading(true)
            
            const formData = new FormData()
            files.forEach((file, index) => {
                formData.append(`images[${index}]`, file)
            })
            
            const response = await ProductService.uploadImages(productId, formData)
            return response
        } catch (err) {
            handleError(err)
            throw err
        } finally {
            setLoading(false)
        }
    }

    // Reset state
    const reset = () => {
        products.value = []
        product.value = null
        error.value = null
        loading.value = false
        pagination.value = {
            current_page: 1,
            per_page: 15,
            total: 0,
            last_page: 1
        }
    }

    return {
        // State
        products,
        product,
        loading,
        error,
        pagination,
        
        // Computed
        hasProducts,
        hasError,
        isLoading,
        
        // Methods
        fetchProducts,
        fetchProduct,
        createProduct,
        updateProduct,
        deleteProduct,
        searchProducts,
        fetchProductVariants,
        fetchProductStock,
        uploadProductImages,
        clearError,
        reset
    }
}