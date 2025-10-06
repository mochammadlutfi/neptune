<template>
    <el-dialog
      v-model="dialogVisible"
      :title="`Send Email: ${emailTemplate?.name || 'Email'}`"
      width="900px"
      :before-close="handleClose"
      class="odoo-email-dialog"
      :close-on-click-modal="false"
      top="5vh"
    >
      <div v-if="emailTemplate" class="odoo-email-composer">
        <!-- Header Actions -->
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
          <div class="flex items-center space-x-2">
            <el-icon class="text-blue-600 text-lg">
              <Message />
            </el-icon>
            <span class="text-sm text-gray-600">
              Template: <strong>{{ emailTemplate.name }}</strong>
            </span>
          </div>
          <div class="flex items-center space-x-2">
            <el-button size="small" plain @click="toggleAdvanced">
              <el-icon class="mr-1">
                <Setting />
              </el-icon>
              {{ showAdvanced ? 'Hide' : 'Show' }} Advanced
            </el-button>
          </div>
        </div>
  
        <!-- Email Composer Form -->
        <el-form :model="emailForm" class="odoo-email-form">
          <!-- Recipients Section -->
          <div class="recipients-section mb-4">
            <div class="grid grid-cols-12 gap-2 items-center mb-2">
              <label class="col-span-1 text-sm font-medium text-gray-700">To:</label>
              <div class="col-span-11">
                <el-input
                  v-model="emailForm.to"
                  placeholder="Enter recipient email addresses (comma separated)"
                  size="default"
                  class="odoo-input"
                >
                  <template #suffix>
                    <el-button text size="small" @click="openContactPicker">
                      <el-icon><User /></el-icon>
                    </el-button>
                  </template>
                </el-input>
              </div>
            </div>
  
            <!-- Advanced Recipients (CC, BCC) -->
            <div v-if="showAdvanced" class="space-y-2">
              <div class="grid grid-cols-12 gap-2 items-center">
                <label class="col-span-1 text-sm font-medium text-gray-700">CC:</label>
                <div class="col-span-11">
                  <el-input
                    v-model="emailForm.cc"
                    placeholder="Carbon copy recipients"
                    size="default"
                    class="odoo-input"
                  />
                </div>
              </div>
              <div class="grid grid-cols-12 gap-2 items-center">
                <label class="col-span-1 text-sm font-medium text-gray-700">BCC:</label>
                <div class="col-span-11">
                  <el-input
                    v-model="emailForm.bcc"
                    placeholder="Blind carbon copy recipients"
                    size="default"
                    class="odoo-input"
                  />
                </div>
              </div>
            </div>
          </div>
  
          <!-- Subject -->
          <div class="grid grid-cols-12 gap-2 items-center mb-4">
            <label class="col-span-1 text-sm font-medium text-gray-700">Subject:</label>
            <div class="col-span-11">
              <el-input
                v-model="processedSubject"
                size="default"
                class="odoo-input subject-input"
                :readonly="!allowSubjectEdit"
              >
                <template #suffix>
                  <el-button text size="small" @click="toggleSubjectEdit">
                    <el-icon><EditPen /></el-icon>
                  </el-button>
                </template>
              </el-input>
            </div>
          </div>
  
          <!-- Body Editor -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Message:</label>
            <div class="odoo-editor-container">
              <div class="editor-toolbar bg-gray-50 border-b px-3 py-2 flex items-center space-x-2">
                <el-button-group size="small">
                  <el-button plain @click="formatText('bold')">
                    <strong>B</strong>
                  </el-button>
                  <el-button plain @click="formatText('italic')">
                    <em>I</em>
                  </el-button>
                  <el-button plain @click="formatText('underline')">
                    <u>U</u>
                  </el-button>
                </el-button-group>
                <el-divider direction="vertical" />
                <el-button size="small" plain @click="insertVariable">
                  <el-icon class="mr-1"><PriceTag /></el-icon>
                  Variables
                </el-button>
              </div>
              <div 
                ref="editorRef"
                class="editor-content p-4 min-h-[200px] max-h-[300px] overflow-y-auto bg-white border-t-0 border border-gray-300 rounded-b"
                contenteditable="true"
                v-html="processedBody"
                @input="updateBody"
              ></div>
            </div>
          </div>
  
          <!-- Attachments Section -->
          <div class="attachments-section mb-4">
            <div class="flex items-center justify-between mb-3">
              <label class="text-sm font-medium text-gray-700">Attachments:</label>
              <div class="flex items-center space-x-2">
                <el-button size="small" plain @click="generatePDF" :loading="generatingPDF">
                  <el-icon class="mr-1"><Document /></el-icon>
                  Generate PDF
                </el-button>
                <el-button size="small" plain @click="uploadFile">
                  <el-icon class="mr-1"><Upload /></el-icon>
                  Upload File
                </el-button>
              </div>
            </div>
            
            <!-- Attachment List -->
            <div v-if="attachments.length > 0" class="attachments-list space-y-2">
              <div 
                v-for="(attachment, index) in attachments" 
                :key="index"
                class="attachment-item flex items-center justify-between p-3 bg-gray-50 rounded border"
              >
                <div class="flex items-center space-x-3">
                  <el-icon class="text-red-500" v-if="attachment.type === 'pdf'">
                    <Document />
                  </el-icon>
                  <el-icon class="text-blue-500" v-else>
                    <Paperclip />
                  </el-icon>
                  <div>
                    <div class="text-sm font-medium">{{ attachment.name }}</div>
                    <div class="text-xs text-gray-500">
                      {{ formatFileSize(attachment.size) }} • {{ attachment.type.toUpperCase() }}
                    </div>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <el-button size="small" text @click="previewAttachment(attachment)">
                    <el-icon><View /></el-icon>
                  </el-button>
                  <el-button size="small" text type="danger" @click="removeAttachment(index)">
                    <el-icon><Delete /></el-icon>
                  </el-button>
                </div>
              </div>
            </div>
            
            <!-- Empty State -->
            <div v-else class="text-center py-4 text-gray-500 text-sm border-2 border-dashed border-gray-200 rounded">
              No attachments. Click "Generate PDF" or "Upload File" to add attachments.
            </div>
          </div>
  
          <!-- Template Variables (Collapsible) -->
          <div v-if="templateVariables.length > 0" class="variables-section">
            <el-collapse v-model="activeCollapse">
              <el-collapse-item name="variables">
                <template #title>
                  <div class="flex items-center space-x-2">
                    <el-icon><PriceTag /></el-icon>
                    <span class="text-sm font-medium">Template Variables ({{ templateVariables.length }})</span>
                  </div>
                </template>
                <div class="grid grid-cols-2 gap-2">
                  <el-tag
                    v-for="variable in templateVariables"
                    :key="variable"
                    size="small"
                    class="cursor-pointer hover:bg-blue-50"
                    @click="insertVariableIntoEditor(variable)"
                  >
                    {{ variable }}
                  </el-tag>
                </div>
              </el-collapse-item>
            </el-collapse>
          </div>
        </el-form>
      </div>
  
      <!-- Loading State -->
      <div v-else class="flex justify-center items-center py-12">
        <el-icon class="is-loading mr-2 text-2xl">
          <Loading />
        </el-icon>
        <span class="text-gray-600">Loading email template...</span>
      </div>
  
      <!-- Footer Actions -->
      <template #footer>
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <el-checkbox v-model="emailForm.sendLater" size="small">
              Send Later
            </el-checkbox>
            <el-date-picker
              v-if="emailForm.sendLater"
              v-model="emailForm.scheduledDate"
              type="datetime"
              placeholder="Schedule sending"
              size="small"
              format="YYYY-MM-DD HH:mm"
            />
          </div>
          <div class="flex items-center space-x-2">
            <el-button @click="handleClose" size="default">
              Cancel
            </el-button>
            <el-button @click="saveDraft" plain size="default">
              Save Draft
            </el-button>
            <el-button 
              type="primary" 
              @click="handleSend"
              :loading="sending"
              :disabled="!emailForm.to || !emailTemplate"
              size="default"
            >
              <el-icon class="mr-1">
                <Promotion />
              </el-icon>
              {{ emailForm.sendLater ? 'Schedule Send' : 'Send Now' }}
            </el-button>
          </div>
        </div>
      </template>
  
      <!-- Hidden file input -->
      <input 
        ref="fileInputRef" 
        type="file" 
        multiple 
        accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg"
        style="display: none" 
        @change="handleFileUpload"
      />
    </el-dialog>
  </template>
  
  <script setup>
  import { ref, computed, watch, nextTick } from 'vue'
  import { ElMessage, ElMessageBox } from 'element-plus'
  import { 
    Message, Loading, Setting, User, EditPen, PriceTag, 
    Document, Upload, Paperclip, View, Delete, Promotion 
  } from '@element-plus/icons-vue'
  
  // Props
  const props = defineProps({
    code: {
      type: String,
      required: true
    },
    data: {
      type: Object,
      default: () => ({})
    },
    modelValue: {
      type: Boolean,
      default: false
    },
    pdfApiUrl: {
      type: String,
      default: '/api/generate-pdf'
    }
  })
  
  // Emits
  const emit = defineEmits(['update:modelValue', 'send', 'save-draft'])
  
  // Email templates data (from your SQL)
  const emailTemplates = [
    {
      id: 1,
      code: 'user.invitation',
      name: 'User Invitation',
      subject: 'You\'re invited to join {{ company_name }}',
      body: '<p>Hello {{ user_name }},<br>You have been invited to join {{ company_name }}.</p>',
      variables: ['user_name', 'company_name']
    },
    {
      id: 2,
      code: 'user.reset_password',
      name: 'Reset Password Request',
      subject: 'Reset Your Password',
      body: '<p>Hi {{ user_name }},<br>Click <a href="{{ reset_link }}">here</a> to reset your password.</p>',
      variables: ['user_name', 'reset_link']
    },
    {
      id: 3,
      code: 'user.password_changed',
      name: 'Password Changed Notification',
      subject: 'Your Password Has Been Changed',
      body: '<p>Your password was successfully changed on {{ changed_at }}.</p>',
      variables: ['changed_at']
    },
    {
      id: 4,
      code: 'user.welcome',
      name: 'Welcome Message',
      subject: 'Welcome to {{ company_name }}',
      body: '<p>Welcome {{ user_name }}!<br>You are now part of {{ company_name }}.</p>',
      variables: ['user_name', 'company_name']
    },
    {
      id: 5,
      code: 'sales.quotation_sent',
      name: 'Sales Quotation Sent',
      subject: 'Your Quotation from {{ company_name }}',
      body: '<p>Dear {{ customer_name }},<br>We have sent you a quotation #{{ quotation_number }}.</p>',
      variables: ['customer_name', 'quotation_number', 'company_name']
    },
    {
      id: 6,
      code: 'sales.order_confirmed',
      name: 'Sales Order Confirmed',
      subject: 'Your Order Has Been Confirmed',
      body: '<p>Your order #{{ order_number }} has been confirmed.</p>',
      variables: ['order_number']
    },
    {
      id: 7,
      code: 'sales.invoice_sent',
      name: 'Sales Invoice Sent',
      subject: 'Your Invoice from {{ company_name }}',
      body: '<p>Invoice #{{ invoice_number }} is now available for payment.</p>',
      variables: ['invoice_number', 'company_name']
    },
    {
      id: 8,
      code: 'sales.payment_received',
      name: 'Customer Payment Received',
      subject: 'We Have Received Your Payment',
      body: '<p>Thank you! We have received your payment for invoice #{{ invoice_number }}.</p>',
      variables: ['invoice_number']
    },
    {
      id: 9,
      code: 'sales.delivery_sent',
      name: 'Sales Delivery Sent',
      subject: 'Your Order Has Been Shipped',
      body: '<p>Your items for order #{{ order_number }} have been shipped.</p>',
      variables: ['order_number']
    },
    {
      id: 10,
      code: 'purchase.order_sent',
      name: 'Purchase Order Sent',
      subject: 'Purchase Order from {{ company_name }}',
      body: '<p>Hello {{ vendor_name }},<br>Here is your purchase order #{{ po_number }}.</p>',
      variables: ['vendor_name', 'po_number', 'company_name']
    },
    {
      id: 11,
      code: 'purchase.bill_received',
      name: 'Vendor Bill Recorded',
      subject: 'Your Bill Has Been Recorded',
      body: '<p>We have recorded your bill #{{ bill_number }}.</p>',
      variables: ['bill_number']
    },
    {
      id: 12,
      code: 'purchase.payment_sent',
      name: 'Vendor Payment Sent',
      subject: 'Payment for Your Invoice',
      body: '<p>We have sent payment for invoice #{{ invoice_number }}.</p>',
      variables: ['invoice_number']
    },
    {
      id: 13,
      code: 'inventory.adjustment_done',
      name: 'Stock Adjustment Completed',
      subject: 'Stock Adjustment Completed',
      body: '<p>Stock adjustment #{{ adjustment_number }} has been completed.</p>',
      variables: ['adjustment_number']
    },
    {
      id: 14,
      code: 'inventory.low_stock_alert',
      name: 'Low Stock Alert',
      subject: 'Low Stock Alert: {{ product_name }}',
      body: '<p>Stock for {{ product_name }} is below the minimum level.</p>',
      variables: ['product_name']
    },
    {
      id: 15,
      code: 'invoice.overdue_notice',
      name: 'Overdue Invoice Notification',
      subject: 'Invoice {{ invoice_number }} is Overdue',
      body: '<p>Invoice #{{ invoice_number }} is overdue. Please make the payment soon.</p>',
      variables: ['invoice_number']
    },
    {
      id: 16,
      code: 'invoice.payment_reminder',
      name: 'Invoice Payment Reminder',
      subject: 'Payment Reminder for Invoice {{ invoice_number }}',
      body: '<p>This is a reminder to pay invoice #{{ invoice_number }} before the due date.</p>',
      variables: ['invoice_number']
    },
    {
      id: 17,
      code: 'approval.request',
      name: 'Approval Request',
      subject: 'Approval Required: {{ document_type }}',
      body: '<p>Please review the document {{ document_type }} #{{ document_number }} for approval.</p>',
      variables: ['document_type', 'document_number']
    },
    {
      id: 18,
      code: 'approval.approved',
      name: 'Approval Approved Notification',
      subject: '{{ document_type }} Approved',
      body: '<p>{{ document_type }} #{{ document_number }} has been approved.</p>',
      variables: ['document_type', 'document_number']
    },
    {
      id: 19,
      code: 'approval.rejected',
      name: 'Approval Rejected Notification',
      subject: '{{ document_type }} Rejected',
      body: '<p>{{ document_type }} #{{ document_number }} has been rejected.</p>',
      variables: ['document_type', 'document_number']
    }
  ]
  
  // Reactive data
  const dialogVisible = ref(false)
  const sending = ref(false)
  const generatingPDF = ref(false)
  const showAdvanced = ref(false)
  const allowSubjectEdit = ref(false)
  const activeCollapse = ref([])
  const editorRef = ref(null)
  const fileInputRef = ref(null)
  
  const emailForm = ref({
    to: '',
    cc: '',
    bcc: '',
    sendLater: false,
    scheduledDate: null
  })
  
  const attachments = ref([])
  
  // Computed properties
  const emailTemplate = computed(() => {
    return emailTemplates.find(template => template.code === props.code)
  })
  
  const templateVariables = computed(() => {
    return emailTemplate.value?.variables || []
  })
  
  const processedSubject = computed({
    get() {
      if (!emailTemplate.value) return ''
      return replaceVariables(emailTemplate.value.subject, props.data)
    },
    set(value) {
      // Allow editing if enabled
    }
  })
  
  const processedBody = computed(() => {
    if (!emailTemplate.value) return ''
    return replaceVariables(emailTemplate.value.body, props.data)
  })
  
  // Methods
  const replaceVariables = (text, data) => {
    let processedText = text
    
    // Replace variables in format {{ variable_name }}
    Object.keys(data).forEach(key => {
      const regex = new RegExp(`\\{\\{\\s*${key}\\s*\\}\\}`, 'g')
      processedText = processedText.replace(regex, data[key] || '')
    })
    
    return processedText
  }
  
  const toggleAdvanced = () => {
    showAdvanced.value = !showAdvanced.value
  }
  
  const toggleSubjectEdit = () => {
    allowSubjectEdit.value = !allowSubjectEdit.value
  }
  
  const openContactPicker = () => {
    // Implement contact picker functionality
    ElMessage.info('Contact picker functionality can be implemented here')
  }
  
  const formatText = (command) => {
    document.execCommand(command, false, null)
    editorRef.value?.focus()
  }
  
  const insertVariable = () => {
    activeCollapse.value = activeCollapse.value.includes('variables') ? [] : ['variables']
  }
  
  const insertVariableIntoEditor = (variable) => {
    const selection = window.getSelection()
    if (selection.rangeCount > 0) {
      const range = selection.getRangeAt(0)
      const variableNode = document.createElement('span')
      variableNode.textContent = `{{ ${variable} }}`
      variableNode.className = 'text-blue-600 bg-blue-50 px-1 rounded'
      range.insertNode(variableNode)
      range.collapse(false)
    }
  }
  
  const updateBody = (event) => {
    // Handle body content changes
  }
  
  const generatePDF = async () => {
    generatingPDF.value = true
    
    try {
      // Call API to generate PDF
      const response = await fetch(props.pdfApiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          template_code: props.code,
          data: props.data
        })
      })
  
      if (!response.ok) throw new Error('Failed to generate PDF')
  
      const blob = await response.blob()
      const fileName = `${props.code.replace('.', '_')}_${Date.now()}.pdf`
      
      attachments.value.push({
        name: fileName,
        type: 'pdf',
        size: blob.size,
        blob: blob,
        url: URL.createObjectURL(blob)
      })
  
      ElMessage.success('PDF generated successfully!')
      
    } catch (error) {
      console.error('Error generating PDF:', error)
      ElMessage.error('Failed to generate PDF')
    } finally {
      generatingPDF.value = false
    }
  }
  
  const uploadFile = () => {
    fileInputRef.value?.click()
  }
  
  const handleFileUpload = (event) => {
    const files = Array.from(event.target.files)
    
    files.forEach(file => {
      attachments.value.push({
        name: file.name,
        type: file.type.split('/')[1] || 'file',
        size: file.size,
        file: file,
        url: URL.createObjectURL(file)
      })
    })
  
    // Clear the input
    event.target.value = ''
    
    ElMessage.success(`${files.length} file(s) uploaded successfully!`)
  }
  
  const previewAttachment = (attachment) => {
    // Open preview in new window
    window.open(attachment.url, '_blank')
  }
  
  const removeAttachment = (index) => {
    const attachment = attachments.value[index]
    URL.revokeObjectURL(attachment.url)
    attachments.value.splice(index, 1)
    ElMessage.success('Attachment removed')
  }
  
  const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
  }
  
  const saveDraft = () => {
    const draftData = {
      template_code: props.code,
      to: emailForm.value.to,
      cc: emailForm.value.cc,
      bcc: emailForm.value.bcc,
      subject: processedSubject.value,
      body: editorRef.value?.innerHTML || processedBody.value,
      attachments: attachments.value,
      data: props.data
    }
  
    emit('save-draft', draftData)
    ElMessage.success('Draft saved successfully!')
  }
  
  const handleClose = () => {
    dialogVisible.value = false
    emit('update:modelValue', false)
    
    // Reset form
    emailForm.value = {
      to: '',
      cc: '',
      bcc: '',
      sendLater: false,
      scheduledDate: null
    }
    
    // Clear attachments
    attachments.value.forEach(attachment => {
      if (attachment.url) {
        URL.revokeObjectURL(attachment.url)
      }
    })
    attachments.value = []
    
    showAdvanced.value = false
    allowSubjectEdit.value = false
    activeCollapse.value = []
  }
  
  const handleSend = async () => {
    if (!emailForm.value.to) {
      ElMessage.warning('Please enter recipient email')
      return
    }
  
    if (!emailTemplate.value) {
      ElMessage.error('Email template not found')
      return
    }
  
    sending.value = true
    
    try {
      const emailData = {
        template_code: props.code,
        to: emailForm.value.to,
        cc: emailForm.value.cc,
        bcc: emailForm.value.bcc,
        subject: processedSubject.value,
        body: editorRef.value?.innerHTML || processedBody.value,
        attachments: attachments.value,
        send_later: emailForm.value.sendLater,
        scheduled_date: emailForm.value.scheduledDate,
        data: props.data
      }
  
      // Emit send event
      emit('send', emailData)
      
      ElMessage.success(emailForm.value.sendLater ? 'Email scheduled successfully!' : 'Email sent successfully!')
      handleClose()
      
    } catch (error) {
      console.error('Error sending email:', error)
      ElMessage.error('Failed to send email')
    } finally {
      sending.value = false
    }
  }
  
  // Watch for modelValue changes
  watch(() => props.modelValue, (newVal) => {
    dialogVisible.value = newVal
  })
  
  watch(dialogVisible, (newVal) => {
    if (!newVal) {
      emit('update:modelValue', false)
    }
  })
  
  // Auto-open dialog when props change
  watch([() => props.code, () => props.data], () => {
    if (props.code && Object.keys(props.data).length > 0) {
      dialogVisible.value = true
      emit('update:modelValue', true)
    }
  }, { immediate: true })
  </script>
  
  <style scoped>
  .odoo-email-dialog :deep(.el-dialog) {
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  }
  
  .odoo-email-dialog :deep(.el-dialog__header) {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px 24px;
    border-radius: 8px 8px 0 0;
  }
  
  .odoo-email-dialog :deep(.el-dialog__title) {
    color: white;
    font-weight: 600;
  }
  
  .odoo-email-dialog :deep(.el-dialog__close) {
    color: white;
  }
  
  .odoo-email-dialog :deep(.el-dialog__body) {
    padding: 24px;
    background: #fafbfc;
  }
  
  .odoo-email-dialog :deep(.el-dialog__footer) {
    background: white;
    padding: 16px 24px;
    border-top: 1px solid #e9ecef;
    border-radius: 0 0 8px 8px;
  }
  
  .odoo-email-composer {
    background: white;
    border-radius: 6px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  }
  
  .odoo-input :deep(.el-input__wrapper) {
    border-radius: 4px;
    border: 1px solid #d0d5dd;
    transition: all 0.2s;
  }
  
  .odoo-input :deep(.el-input__wrapper):hover {
    border-color: #667eea;
  }
  
  .odoo-input :deep(.el-input__wrapper.is-focus) {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  }
  
  .subject-input :deep(.el-input__wrapper) {
    background-color: #f8f9fa;
  }
  
  .odoo-editor-container {
    border: 1px solid #d0d5dd;
    border-radius: 6px;
    overflow: hidden;
  }
  
  .editor-toolbar {
    border-bottom: 1px solid #e9ecef;
  }
  
  .editor-content {
    outline: none;
    line-height: 1.6;
  }
  
  .editor-content:focus {
    box-shadow: inset 0 0 0 2px rgba(102, 126, 234, 0.1);
  }
  
  .attachment-item {
    transition: all 0.2s;
  }
  
  .attachment-item:hover {
    background-color: #f1f3f4;
    border-color: #667eea;
  }
  
  .recipients-section label {
    padding-top: 8px;
  }
  
  .variables-section {
    margin-top: 16px;
  }
  
  .variables-section :deep(.el-collapse-item__header) {
    background-color: #f8f9fa;
    padding-left: 16px;
    border-radius: 4px;
  }
  
  .variables-section :deep(.el-collapse-item__content) {
    padding: 16px;
    background-color: #f8f9fa;
    border-radius: 0 0 4px 4px;
  }
  </style>