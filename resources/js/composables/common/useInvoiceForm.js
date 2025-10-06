// resources/js/composables/useInvoiceForm.js
import { computed } from 'vue';
import { useSetting } from "./useSetting";

export function useInvoiceForm(form, app) {
    const appBase = useSetting();
    const appData = computed(() => app?.value || appBase.app);

    const searchOrder = async (order_id, type = 'invoice') => {
        try {
            let url = '';
            if (type === 'vendor_bill') {
                url = `/purchase/order/${order_id}`;
            } else if (type === 'credit_note') {
                url = `/sales/return/${order_id}`;
            } else if (type === 'debit_note') {
                url = `/purchase/return/${order_id}`;
            } else {
                url = `/sales/order/${order_id}`;
            }

            const response = await axios.get(url);
            if (response.status === 200) {
                const result = response.data.result;
                result.lines.forEach(line => {
                    let qty = 0;
                    
                    if (line.qty_invoiced >= line.qty) {
                        return;
                    }

                    if (type === 'vendor_bill') {
                        if (appData.value.purchase.bill_control === 'ordered_qty') {
                            qty = line.qty - line.qty_billed;
                        } else {
                            qty = line.qty_received - line.qty_billed;
                        }
                    } else if (type === 'credit_note') {
                        qty = line.qty_returned - line.qty_invoiced;
                    } else if (type === 'debit_note') {
                        qty = line.qty_returned - line.qty_billed;
                    } else {
                        if (appData.value.sales.invoicing_policy === 'ordered_qty') {
                            qty = line.qty - line.qty_invoiced;
                        } else {
                            qty = line.qty_delivered - line.qty_invoiced;
                        }
                    }

                    if (qty > 0) {
                        onSelectProduct({
                            origintable_id: line.id,
                            order_id: line.order_id,
                            order: result.name,
                            product_id: line.product_id,
                            id: line.variant_id,
                            product: line.product.name,
                            stock_control: line.product.stock_control,
                            name: line.variant.name,
                            type: 'product',
                            unit: line.unit,
                            unit_id: line.unit_id,
                            base_unit_id: line.unit_id,
                            price: line.price_unit,
                            discount: line.discount,
                            tax: line.tax,
                            qty: qty,
                            disc_type: line.disc_type,
                            disc_value: line.disc_value,
                            price_disc: line.price_disc,
                            price_tax: line.price_tax,
                            price_subtotal: line.price_subtotal,
                            price_total: line.price_total,
                        });
                    }
                });
            }
        } catch (error) {
            console.error(error);
        }
    };

    const handleOriginChange = async (value, type = 'invoice') => {
        // Ambil order_id yang sudah ada di form.lines
        const existingOrderIds = form.value.lines.map(line => line.order_id);

        // Order yang baru ditambahkan
        const added = value.filter(id => !existingOrderIds.includes(id));

        // Order yang dihapus
        const removed = existingOrderIds.filter(id => !value.includes(id));

        // Fetch order detail untuk order yang baru ditambahkan
        for (const orderId of added) {
            await searchOrder(orderId, type);
        }

        // Hapus semua line yang berasal dari order yang dihapus
        form.value.lines = form.value.lines.filter(line => !removed.includes(line.order_id));
    };

    const onSelectProduct = (data) => {
        let account = null;
        const type = form.value.type;

        if (type === 'invoice') {
            account = appData.value.accounting.account_revenue;
        } else if (type === 'vendor_bill') {
            if (data.stock_control) {
                account = appData.value.accounting.account_inventory_in;
            } else {
                account = appData.value.accounting.account_expense;
            }
        } else if (type === 'credit_note') {
            account = appData.value.accounting.account_revenue;
        } else if (type === 'debit_note') {
            account = appData.value.accounting.account_expense;
        }

        if (form.value.lines.length >= 1 && form.value.lines.some(detail => 
            (detail.variant_id === data.id && detail.origintable_id === data.origintable_id && detail.order_id === data.order_id))) {
            for (var i = 0; i < form.value.lines.length; i++) {
                if (form.value.lines[i].variant_id === data.id) {
                    form.value.lines[i].qty++;
                }
            }
        } else {
            form.value.lines.push({
                id: null,
                origintable_id: data.origintable_id ?? null,
                order_id: data.order_id ?? null,
                order: data.order,
                product_id: data.product_id,
                variant_id: data.id,
                variant: data.name,
                product: data.product,
                name: data.product + (data.variant ? `(${data.variant})` : ''),
                type: 'product',
                account: account,
                unit: data.unit,
                unit_id: data.unit_id,
                base_unit_id: data.unit_id,
                qty: data.qty ?? 1,
                disc_type: data.disc_type ?? 'percentage',
                disc_value: data.disc_value ?? 0,
                tax: data.tax ?? null,
                price_unit: data.price,
                price_disc: data.price_disc ?? 0,
                price_subtotal: data.price_subtotal ?? 0,
                price_tax: data.price_tax ?? 0,
                price_total: data.price_total ?? 0,
                debit: 0,
                credit: 0,
                sequence: 10
            });
        }
        calculateTotal();
    };

    const calculateTotal = () => {
        let amount_untaxed = 0;
        let amount_tax = 0;
        let amount_disc = 0;
        let account = null;
        let accountTax = null;
        let accountDiscount = null;
        const type = form.value.type;

        // 1. Hapus semua tax journal yang ada
        form.value.lines = form.value.lines.filter(line => line.type !== 'tax');

        // 2. Buat map untuk mengumpulkan tax per jenis
        const taxMap = new Map();
        const orderList = [];
        const originList = [];

        if (type === 'invoice' || type === 'credit_note') {
            accountTax = appData.value.accounting.account_tax_collected;
            accountDiscount = appData.value.accounting.account_sales_discount;
            account = type === 'credit_note' ? appData.value.accounting.account_receivable : appData.value.accounting.account_receivable;
        } else if (type === 'vendor_bill' || type === 'debit_note') {
            accountTax = appData.value.accounting.account_tax_expense;
            accountDiscount = appData.value.accounting.account_purchase_discount;
            account = appData.value.accounting.account_payable;
        }

        form.value.lines.forEach(line => {
            if (line.type === 'product') {
                const price = line.disc_value ? line.price_unit - line.price_disc : line.price_unit;
                line.price_subtotal = line.qty * price;

                if (type === 'invoice' || type === 'credit_note') {
                    line.credit = line.price_subtotal;
                    line.debit = 0;
                } else if (type === 'vendor_bill' || type === 'debit_note') {
                    line.debit = line.price_subtotal;
                    line.credit = 0;
                }

                // Handle tax
                if (line.tax) {
                    line.price_tax = price * line.tax.rate / 100;

                    // Akumulasi tax per jenis
                    if (taxMap.has(line.tax.id)) {
                        taxMap.get(line.tax.id).amount += line.price_tax * line.qty;
                    } else {
                        taxMap.set(line.tax.id, {
                            name: `${line.tax.name} (${line.tax.rate}%)`,
                            tax: line.tax,
                            amount: line.price_tax * line.qty
                        });
                    }
                } else {
                    line.price_tax = 0;
                    line.tax_id = null;
                }

                if (line.order) {
                    if (!orderList.includes(line.order)) {
                        orderList.push(line.order);
                    }
                    if (!originList.includes(line.order_id)) {
                        originList.push(line.order_id);
                    }
                }

                amount_untaxed += line.price_subtotal;
                amount_disc += line.price_disc * line.qty;
                amount_tax += line.price_tax * line.qty;
            }
        });

        form.value.amount_untaxed = amount_untaxed;
        form.value.amount_tax = amount_tax;
        form.value.amount_total = amount_untaxed + amount_tax;

        // 3. Tambahkan tax journal untuk setiap jenis tax
        taxMap.forEach(tax => {
            let debitTax = 0, creditTax = 0;

            if (type === 'invoice' || type === 'credit_note') {
                creditTax = tax.amount;
            } else if (type === 'vendor_bill' || type === 'debit_note') {
                debitTax = tax.amount;
            }

            form.value.lines.push({
                name: tax.name,
                type: 'tax',
                tax: tax.tax,
                account: accountTax,
                credit: creditTax,
                debit: debitTax,
                sequence: 900
            });
        });

        // 4. Handle discount
        if (amount_disc) {
            let debitDiscount = 0, creditDiscount = 0;

            if (type === 'invoice' || type === 'credit_note') {
                debitDiscount = amount_disc;
            } else if (type === 'vendor_bill' || type === 'debit_note') {
                creditDiscount = amount_disc;
            }

            const disc = form.value.lines.find(c => c.type === 'discount');
            if (disc) {
                disc.debit = debitDiscount;
                disc.credit = creditDiscount;
            } else {
                form.value.lines.push({
                    name: 'Discount',
                    type: 'discount',
                    account: accountDiscount,
                    debit: debitDiscount,
                    credit: creditDiscount,
                    sequence: 800
                });
            }
        }

        // 5. Update payment terms
        let debitTerm = 0, creditTerm = 0;

        if (type === 'invoice' || type === 'credit_note') {
            debitTerm = form.value.amount_total - amount_disc;
        } else if (type === 'vendor_bill' || type === 'debit_note') {
            creditTerm = form.value.amount_total - amount_disc;
        }

        const paymentTerm = form.value.lines.find(line => line.type === 'payment_terms');
        if (paymentTerm) {
            paymentTerm.debit = debitTerm;
            paymentTerm.credit = creditTerm;
        } else {
            form.value.lines.push({
                type: 'payment_terms',
                account: account,
                debit: debitTerm,
                credit: creditTerm,
                sequence: 1200
            });
        }

        // 6. Urutkan lines berdasarkan sequence
        form.value.lines.sort((a, b) => a.sequence - b.sequence);
    };

    const sumJournal = (v) => {
        const { columns, data } = v;
        const sums = [];

        columns.forEach((column, index) => {
            if (['debit', 'credit'].includes(column.property)) {
                const total = data.reduce((acc, item) => {
                    const value = Number(item[column.property]) || 0;
                    return acc + value;
                }, 0);

                sums[index] = formatCurrency(total);
            } else {
                sums[index] = '';
            }
        });

        return sums;
    };

    return {
        searchOrder,
        handleOriginChange,
        onSelectProduct,
        calculateTotal,
        sumJournal
    };
}