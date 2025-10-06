import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import { computed } from 'vue'
import { ProductService } from '@/Services'

/**
 * Products Composable with TanStack Query Integration
 * Provides reactive product data and operations with smart caching
 */
export function useProducts() {
    const queryClient = useQueryClient()

    // Query Keys
    const QUERY_KEYS = {
        products: (params = {}) => ['products', params],
        product: (id) => ['products', id],
        variants: (productId) => ['products', productId, 'variants'],
        stock: (productId) => ['products', productId, 'stock'],
        categories: () => ['product-categories'],
        brands: () => ['brands']
    }

    // Queries
    const useProductsQuery = (params = {}, options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.products(params),
            queryFn: () => ProductService.getAll(params),
            staleTime: 5 * 60 * 1000, // 5 minutes
            ...options
        })
    }

    const useProductQuery = (id, options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.product(id),
            queryFn: () => ProductService.getById(id),
            enabled: !!id,
            staleTime: 10 * 60 * 1000, // 10 minutes
            ...options
        })
    }

    const useProductVariantsQuery = (productId, options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.variants(productId),
            queryFn: () => ProductService.getVariants(productId),
            enabled: !!productId,
            staleTime: 10 * 60 * 1000,
            ...options
        })
    }

    const useProductStockQuery = (productId, options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.stock(productId),
            queryFn: () => ProductService.getStock(productId),
            enabled: !!productId,
            staleTime: 1 * 60 * 1000, // 1 minute (stock data changes frequently)
            ...options
        })
    }

    const useCategoriesQuery = (options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.categories(),
            queryFn: () => ProductService.getCategories(),
            staleTime: 30 * 60 * 1000, // 30 minutes (categories don't change often)
            ...options
        })
    }

    const useBrandsQuery = (options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.brands(),
            queryFn: () => ProductService.getBrands(),
            staleTime: 30 * 60 * 1000,
            ...options
        })
    }

    // Mutations
    const useCreateProductMutation = (options = {}) => {
        return useMutation({
            mutationFn: (data) => ProductService.create(data),
            onSuccess: (data, variables) => {
                // Invalidate products list
                queryClient.invalidateQueries({ queryKey: ['products'] })
                
                // Add to cache
                queryClient.setQueryData(QUERY_KEYS.product(data.id), data)
                
                options.onSuccess?.(data, variables)
            },
            onError: options.onError,
            ...options
        })
    }

    const useUpdateProductMutation = (options = {}) => {
        return useMutation({
            mutationFn: ({ id, data }) => ProductService.update(id, data),
            onSuccess: (data, variables) => {
                // Update specific product in cache
                queryClient.setQueryData(QUERY_KEYS.product(variables.id), data)
                
                // Invalidate products list to refresh
                queryClient.invalidateQueries({ queryKey: ['products'] })
                
                options.onSuccess?.(data, variables)
            },
            onError: options.onError,
            ...options
        })
    }

    const useDeleteProductMutation = (options = {}) => {
        return useMutation({
            mutationFn: (id) => ProductService.remove(id),
            onSuccess: (data, variables) => {
                // Remove from cache
                queryClient.removeQueries({ queryKey: QUERY_KEYS.product(variables) })
                
                // Invalidate products list
                queryClient.invalidateQueries({ queryKey: ['products'] })
                
                options.onSuccess?.(data, variables)
            },
            onError: options.onError,
            ...options
        })
    }

    const useUploadProductImagesMutation = (options = {}) => {
        return useMutation({
            mutationFn: ({ productId, files }) => {
                const formData = new FormData()
                files.forEach((file, index) => {
                    formData.append(`images[${index}]`, file)
                })
                return ProductService.uploadImages(productId, formData)
            },
            onSuccess: (data, variables) => {
                // Invalidate product data to refresh images
                queryClient.invalidateQueries({ 
                    queryKey: QUERY_KEYS.product(variables.productId) 
                })
                
                options.onSuccess?.(data, variables)
            },
            onError: options.onError,
            ...options
        })
    }

    // Optimistic Update Mutations
    const useOptimisticUpdateProductMutation = (options = {}) => {
        return useMutation({
            mutationFn: ({ id, data }) => ProductService.update(id, data),
            onMutate: async ({ id, data }) => {
                // Cancel outgoing refetches
                await queryClient.cancelQueries({ queryKey: QUERY_KEYS.product(id) })

                // Snapshot previous value
                const previousProduct = queryClient.getQueryData(QUERY_KEYS.product(id))

                // Optimistically update
                queryClient.setQueryData(QUERY_KEYS.product(id), old => ({
                    ...old,
                    ...data
                }))

                return { previousProduct, id }
            },
            onError: (err, variables, context) => {
                // Rollback on error
                if (context?.previousProduct) {
                    queryClient.setQueryData(
                        QUERY_KEYS.product(context.id), 
                        context.previousProduct
                    )
                }
                options.onError?.(err, variables, context)
            },
            onSettled: (data, error, variables) => {
                // Always refetch after success or error
                queryClient.invalidateQueries({ 
                    queryKey: QUERY_KEYS.product(variables.id) 
                })
            },
            ...options
        })
    }

    // Search functionality
    const useSearchProducts = (query, options = {}) => {
        return useQuery({
            queryKey: ['products', 'search', query],
            queryFn: () => ProductService.search(query),
            enabled: !!query && query.length >= 2,
            staleTime: 30 * 1000, // 30 seconds
            ...options
        })
    }

    // Infinite Query for pagination
    const useInfiniteProductsQuery = (params = {}, options = {}) => {
        return useInfiniteQuery({
            queryKey: ['products', 'infinite', params],
            queryFn: ({ pageParam = 1 }) => 
                ProductService.getAll({ ...params, page: pageParam }),
            getNextPageParam: (lastPage) => {
                return lastPage.current_page < lastPage.last_page 
                    ? lastPage.current_page + 1 
                    : undefined
            },
            staleTime: 5 * 60 * 1000,
            ...options
        })
    }

    // Helper functions
    const invalidateProducts = () => {
        queryClient.invalidateQueries({ queryKey: ['products'] })
    }

    const prefetchProduct = (id) => {
        queryClient.prefetchQuery({
            queryKey: QUERY_KEYS.product(id),
            queryFn: () => ProductService.getById(id),
            staleTime: 10 * 60 * 1000
        })
    }

    return {
        // Query Keys
        QUERY_KEYS,
        
        // Queries
        useProductsQuery,
        useProductQuery,
        useProductVariantsQuery,
        useProductStockQuery,
        useCategoriesQuery,
        useBrandsQuery,
        useSearchProducts,
        useInfiniteProductsQuery,
        
        // Mutations
        useCreateProductMutation,
        useUpdateProductMutation,
        useDeleteProductMutation,
        useUploadProductImagesMutation,
        useOptimisticUpdateProductMutation,
        
        // Helpers
        invalidateProducts,
        prefetchProduct
    }
}