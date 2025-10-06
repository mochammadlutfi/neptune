import { useQuery, useMutation, useQueryClient } from '@tanstack/vue-query'
import { SalesService } from '@/Services'

/**
 * Sales Composable with TanStack Query Integration
 */
export function useSales() {
    const queryClient = useQueryClient()

    // Query Keys
    const QUERY_KEYS = {
        orders: (params = {}) => ['sales', 'orders', params],
        order: (id) => ['sales', 'orders', id],
        invoices: (params = {}) => ['sales', 'invoices', params],
        invoice: (id) => ['sales', 'invoices', id],
        payments: (params = {}) => ['sales', 'payments', params],
        returns: (params = {}) => ['sales', 'returns', params],
        customers: (params = {}) => ['customers', params],
        customer: (id) => ['customers', id],
        dashboard: (params = {}) => ['sales', 'dashboard', params],
        reports: (type, params = {}) => ['sales', 'reports', type, params]
    }

    // Sales Orders Queries
    const useSalesOrdersQuery = (params = {}, options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.orders(params),
            queryFn: () => SalesService.getOrders(params),
            staleTime: 2 * 60 * 1000, // 2 minutes
            ...options
        })
    }

    const useSalesOrderQuery = (id, options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.order(id),
            queryFn: () => SalesService.getOrderById(id),
            enabled: !!id,
            staleTime: 5 * 60 * 1000,
            ...options
        })
    }

    // Invoices Queries
    const useInvoicesQuery = (params = {}, options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.invoices(params),
            queryFn: () => SalesService.getInvoices(params),
            staleTime: 2 * 60 * 1000,
            ...options
        })
    }

    const useInvoiceQuery = (id, options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.invoice(id),
            queryFn: () => SalesService.getInvoiceById(id),
            enabled: !!id,
            staleTime: 5 * 60 * 1000,
            ...options
        })
    }

    // Payments Query
    const usePaymentsQuery = (params = {}, options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.payments(params),
            queryFn: () => SalesService.getPayments(params),
            staleTime: 1 * 60 * 1000, // 1 minute (payments change frequently)
            ...options
        })
    }

    // Customers Queries
    const useCustomersQuery = (params = {}, options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.customers(params),
            queryFn: () => SalesService.getCustomers(params),
            staleTime: 10 * 60 * 1000, // 10 minutes
            ...options
        })
    }

    const useCustomerQuery = (id, options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.customer(id),
            queryFn: () => SalesService.getCustomerById(id),
            enabled: !!id,
            staleTime: 10 * 60 * 1000,
            ...options
        })
    }

    // Dashboard Query
    const useSalesDashboardQuery = (params = {}, options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.dashboard(params),
            queryFn: () => SalesService.getSalesDashboard(params),
            staleTime: 5 * 60 * 1000,
            ...options
        })
    }

    // Reports Queries
    const useSalesReportQuery = (params = {}, options = {}) => {
        return useQuery({
            queryKey: QUERY_KEYS.reports('sales', params),
            queryFn: () => SalesService.getSalesReport(params),
            staleTime: 10 * 60 * 1000,
            ...options
        })
    }

    // Sales Orders Mutations
    const useCreateSalesOrderMutation = (options = {}) => {
        return useMutation({
            mutationFn: (data) => SalesService.createOrder(data),
            onSuccess: (data, variables) => {
                queryClient.invalidateQueries({ queryKey: ['sales', 'orders'] })
                queryClient.setQueryData(QUERY_KEYS.order(data.id), data)
                options.onSuccess?.(data, variables)
            },
            ...options
        })
    }

    const useUpdateSalesOrderMutation = (options = {}) => {
        return useMutation({
            mutationFn: ({ id, data }) => SalesService.updateOrder(id, data),
            onSuccess: (data, variables) => {
                queryClient.setQueryData(QUERY_KEYS.order(variables.id), data)
                queryClient.invalidateQueries({ queryKey: ['sales', 'orders'] })
                options.onSuccess?.(data, variables)
            },
            ...options
        })
    }

    const useConfirmSalesOrderMutation = (options = {}) => {
        return useMutation({
            mutationFn: (id) => SalesService.confirmOrder(id),
            onSuccess: (data, variables) => {
                queryClient.invalidateQueries({ queryKey: QUERY_KEYS.order(variables) })
                queryClient.invalidateQueries({ queryKey: ['sales', 'orders'] })
                options.onSuccess?.(data, variables)
            },
            ...options
        })
    }

    const useCancelSalesOrderMutation = (options = {}) => {
        return useMutation({
            mutationFn: (id) => SalesService.cancelOrder(id),
            onSuccess: (data, variables) => {
                queryClient.invalidateQueries({ queryKey: QUERY_KEYS.order(variables) })
                queryClient.invalidateQueries({ queryKey: ['sales', 'orders'] })
                options.onSuccess?.(data, variables)
            },
            ...options
        })
    }

    // Invoice Mutations
    const useCreateInvoiceMutation = (options = {}) => {
        return useMutation({
            mutationFn: (data) => SalesService.createInvoice(data),
            onSuccess: (data, variables) => {
                queryClient.invalidateQueries({ queryKey: ['sales', 'invoices'] })
                queryClient.setQueryData(QUERY_KEYS.invoice(data.id), data)
                options.onSuccess?.(data, variables)
            },
            ...options
        })
    }

    const usePostInvoiceMutation = (options = {}) => {
        return useMutation({
            mutationFn: (id) => SalesService.postInvoice(id),
            onSuccess: (data, variables) => {
                queryClient.invalidateQueries({ queryKey: QUERY_KEYS.invoice(variables) })
                queryClient.invalidateQueries({ queryKey: ['sales', 'invoices'] })
                // Also invalidate payments since invoice status affects payment
                queryClient.invalidateQueries({ queryKey: ['sales', 'payments'] })
                options.onSuccess?.(data, variables)
            },
            ...options
        })
    }

    const useSendInvoiceMutation = (options = {}) => {
        return useMutation({
            mutationFn: ({ id, data }) => SalesService.sendInvoice(id, data),
            onSuccess: (data, variables) => {
                queryClient.invalidateQueries({ queryKey: QUERY_KEYS.invoice(variables.id) })
                options.onSuccess?.(data, variables)
            },
            ...options
        })
    }

    // Payment Mutations
    const useCreatePaymentMutation = (options = {}) => {
        return useMutation({
            mutationFn: (data) => SalesService.createPayment(data),
            onSuccess: (data, variables) => {
                queryClient.invalidateQueries({ queryKey: ['sales', 'payments'] })
                // Invalidate related invoice
                if (variables.invoice_id) {
                    queryClient.invalidateQueries({ 
                        queryKey: QUERY_KEYS.invoice(variables.invoice_id) 
                    })
                }
                options.onSuccess?.(data, variables)
            },
            ...options
        })
    }

    const useConfirmPaymentMutation = (options = {}) => {
        return useMutation({
            mutationFn: (id) => SalesService.confirmPayment(id),
            onSuccess: (data, variables) => {
                queryClient.invalidateQueries({ queryKey: ['sales', 'payments'] })
                queryClient.invalidateQueries({ queryKey: ['sales', 'invoices'] })
                options.onSuccess?.(data, variables)
            },
            ...options
        })
    }

    // Customer Mutations
    const useCreateCustomerMutation = (options = {}) => {
        return useMutation({
            mutationFn: (data) => SalesService.createCustomer(data),
            onSuccess: (data, variables) => {
                queryClient.invalidateQueries({ queryKey: ['customers'] })
                queryClient.setQueryData(QUERY_KEYS.customer(data.id), data)
                options.onSuccess?.(data, variables)
            },
            ...options
        })
    }

    const useUpdateCustomerMutation = (options = {}) => {
        return useMutation({
            mutationFn: ({ id, data }) => SalesService.updateCustomer(id, data),
            onSuccess: (data, variables) => {
                queryClient.setQueryData(QUERY_KEYS.customer(variables.id), data)
                queryClient.invalidateQueries({ queryKey: ['customers'] })
                options.onSuccess?.(data, variables)
            },
            ...options
        })
    }

    // Helper functions
    const invalidateSalesData = () => {
        queryClient.invalidateQueries({ queryKey: ['sales'] })
    }

    const prefetchSalesOrder = (id) => {
        queryClient.prefetchQuery({
            queryKey: QUERY_KEYS.order(id),
            queryFn: () => SalesService.getOrderById(id),
            staleTime: 5 * 60 * 1000
        })
    }

    const prefetchCustomer = (id) => {
        queryClient.prefetchQuery({
            queryKey: QUERY_KEYS.customer(id),
            queryFn: () => SalesService.getCustomerById(id),
            staleTime: 10 * 60 * 1000
        })
    }

    return {
        // Query Keys
        QUERY_KEYS,
        
        // Queries
        useSalesOrdersQuery,
        useSalesOrderQuery,
        useInvoicesQuery,
        useInvoiceQuery,
        usePaymentsQuery,
        useCustomersQuery,
        useCustomerQuery,
        useSalesDashboardQuery,
        useSalesReportQuery,
        
        // Sales Order Mutations
        useCreateSalesOrderMutation,
        useUpdateSalesOrderMutation,
        useConfirmSalesOrderMutation,
        useCancelSalesOrderMutation,
        
        // Invoice Mutations
        useCreateInvoiceMutation,
        usePostInvoiceMutation,
        useSendInvoiceMutation,
        
        // Payment Mutations
        useCreatePaymentMutation,
        useConfirmPaymentMutation,
        
        // Customer Mutations
        useCreateCustomerMutation,
        useUpdateCustomerMutation,
        
        // Helpers
        invalidateSalesData,
        prefetchSalesOrder,
        prefetchCustomer
    }
}