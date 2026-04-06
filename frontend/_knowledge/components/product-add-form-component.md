# ProductAddForm Component Snapshot

**Overview**

This snapshot documents a unified ProductAddForm component that could serve as a container/wrapper for the product add/edit form. While the current implementation uses individual component sections (ProductInformation, ProductImage, etc.), this document provides a reusable component pattern for managing complex multi-section forms with validation, submission, and state management.

The ProductAddForm component demonstrates modern React patterns for form handling including:
- Controlled form inputs with validation
- Composition-based architecture with sub-components
- Server action integration for form submission
- Progressive enhancement with loading states
- Error handling and display
- Complex nested form sections

**Use Case**: Creating reusable form containers for products, orders, customers, and other business entities

---

## Component API & TypeScript Definitions

\\\	ypescript
// Product form section types
export type ProductBasicInfo = {
  productName: string
  sku: string
  barcode: string
  description: string
}

export type ProductImageData = {
  files: File[]
  mediaUrls: string[]
}

export type ProductVariant = {
  id: string
  optionType: 'Size' | 'Color' | 'Weight' | 'Smell'
  optionValue: string
}

export type ProductInventoryData = {
  restock: {
    quantityToAdd: number
    stockStatus: InventoryStatus
  }
  shipping: {
    fulfillmentType: 'seller' | 'company'
  }
  globalDelivery: {
    deliveryScope: 'worldwide' | 'selected' | 'local'
    selectedCountries?: string[]
  }
  attributes: {
    isFragile: boolean
    isBiodegradable: boolean
    isFrozen: boolean
    frozenTemperature?: string
    hasExpiry: boolean
    expiryDate?: Date
  }
  advanced: {
    productIdType: 'ISBN' | 'UPC' | 'EAN' | 'JAN'
    productIdValue: string
  }
}

export type ProductPricingData = {
  basePrice: string
  discountedPrice: string
  chargeTax: boolean
  inStock: boolean
}

export type ProductOrganizeData = {
  vendor: string
  category: string
  collection: string
  status: 'Published' | 'Inactive' | 'Scheduled'
  tags: string[]
}

export type ProductFormData = {
  basicInfo: ProductBasicInfo
  images: ProductImageData
  variants: ProductVariant[]
  inventory: ProductInventoryData
  pricing: ProductPricingData
  organize: ProductOrganizeData
}

export type ProductAddFormProps = {
  initialData?: Partial<ProductFormData>
  onSubmit?: (data: ProductFormData) => Promise<void> | void
  submitAction?: (data: ProductFormData) => Promise<{ success: boolean; error?: string }>
  isLoading?: boolean
  submitLabel?: string
  onCancel?: () => void
  onSaveDraft?: (data: ProductFormData) => Promise<void>
}

export type InventoryStatus = {
  inStockNow: number
  inTransit: number
  lastRestocked: string
  totalOverLifetime: number
}

export type FormValidationError = {
  field: string
  message: string
}

export type FormSubmitResult = {
  success: boolean
  errors?: FormValidationError[]
  data?: ProductFormData
}
\\\

---

## Component Structure

\\\	ypescript
import { useState, useCallback, useRef } from 'react'
import { useActionState } from 'react'

// Sub-component exports for composition
export { ProductInformation } from './sections/ProductInformation'
export { ProductImage } from './sections/ProductImage'
export { ProductVariants } from './sections/ProductVariants'
export { ProductInventory } from './sections/ProductInventory'
export { ProductPricing } from './sections/ProductPricing'
export { ProductOrganize } from './sections/ProductOrganize'
export { ProductAddHeader } from './sections/ProductAddHeader'

const ProductAddForm: React.FC<ProductAddFormProps> = ({
  initialData,
  onSubmit,
  submitAction,
  isLoading = false,
  submitLabel = 'Publish Product',
  onCancel,
  onSaveDraft
}) => {
  // Form state management
  const [formData, setFormData] = useState<ProductFormData>(
    initialData || getDefaultFormData()
  )
  const [errors, setErrors] = useState<FormValidationError[]>([])
  const [isSubmitting, setIsSubmitting] = useState(false)
  const [submitResult, setSubmitResult] = useState<FormSubmitResult | null>(null)
  const formRef = useRef<HTMLFormElement>(null)

  // Handler for updating individual form sections
  const handleSectionChange = useCallback(
    (section: keyof ProductFormData, data: any) => {
      setFormData(prev => ({
        ...prev,
        [section]: {
          ...prev[section],
          ...data
        }
      }))
      // Clear errors for this section
      setErrors(prev => prev.filter(e => !e.field.startsWith(section)))
    },
    []
  )

  // Validation logic
  const validateForm = useCallback((): FormValidationError[] => {
    const newErrors: FormValidationError[] = []

    // Validate basic info
    if (!formData.basicInfo.productName.trim()) {
      newErrors.push({ field: 'basicInfo.productName', message: 'Product name is required' })
    }
    if (!formData.basicInfo.sku.trim()) {
      newErrors.push({ field: 'basicInfo.sku', message: 'SKU is required' })
    }

    // Validate pricing
    if (!formData.pricing.basePrice) {
      newErrors.push({ field: 'pricing.basePrice', message: 'Base price is required' })
    }
    if (isNaN(parseFloat(formData.pricing.basePrice))) {
      newErrors.push({ field: 'pricing.basePrice', message: 'Base price must be a valid number' })
    }

    // Validate organization
    if (!formData.organize.vendor) {
      newErrors.push({ field: 'organize.vendor', message: 'Vendor is required' })
    }
    if (!formData.organize.category) {
      newErrors.push({ field: 'organize.category', message: 'Category is required' })
    }
    if (!formData.organize.status) {
      newErrors.push({ field: 'organize.status', message: 'Status is required' })
    }

    return newErrors
  }, [formData])

  // Form submission handler
  const handleSubmit = useCallback(
    async (e: React.FormEvent) => {
      e.preventDefault()

      // Validate form
      const formErrors = validateForm()
      if (formErrors.length > 0) {
        setErrors(formErrors)
        setSubmitResult({ success: false, errors: formErrors })
        return
      }

      setIsSubmitting(true)
      setErrors([])

      try {
        // Use provided submit action or callback
        if (submitAction) {
          const result = await submitAction(formData)
          if (result.success) {
            setSubmitResult({ success: true, data: formData })
            // Could reset form or navigate here
          } else {
            setSubmitResult(result)
          }
        } else if (onSubmit) {
          await onSubmit(formData)
          setSubmitResult({ success: true, data: formData })
        }
      } catch (error) {
        const errorMessage = error instanceof Error ? error.message : 'An error occurred'
        setSubmitResult({
          success: false,
          errors: [{ field: 'form', message: errorMessage }]
        })
      } finally {
        setIsSubmitting(false)
      }
    },
    [formData, validateForm, submitAction, onSubmit]
  )

  // Draft save handler
  const handleSaveDraft = useCallback(async () => {
    try {
      if (onSaveDraft) {
        await onSaveDraft(formData)
        setSubmitResult({ success: true, data: formData })
      }
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : 'Failed to save draft'
      setSubmitResult({
        success: false,
        errors: [{ field: 'form', message: errorMessage }]
      })
    }
  }, [formData, onSaveDraft])

  // Get error for specific field
  const getFieldError = useCallback(
    (fieldPath: string): string | undefined => {
      return errors.find(e => e.field === fieldPath)?.message
    },
    [errors]
  )

  return (
    <form ref={formRef} onSubmit={handleSubmit} className='space-y-6'>
      {/* Header Section */}
      <ProductAddHeader
        onDiscard={onCancel}
        onSaveDraft={handleSaveDraft}
        submitLabel={submitLabel}
        isLoading={isSubmitting || isLoading}
      />

      {/* Error Display */}
      {submitResult && !submitResult.success && submitResult.errors && (
        <ErrorAlert errors={submitResult.errors} />
      )}

      {/* Success Message */}
      {submitResult?.success && (
        <SuccessAlert
          message='Product saved successfully!'
          onDismiss={() => setSubmitResult(null)}
        />
      )}

      {/* Main Content Grid */}
      <Grid container spacing={6}>
        {/* Left Column - Main Form Sections */}
        <Grid size={{ xs: 12, md: 8 }}>
          <Grid container spacing={6}>
            {/* Product Information Section */}
            <Grid size={{ xs: 12 }}>
              <ProductInformation
                data={formData.basicInfo}
                onChange={data => handleSectionChange('basicInfo', data)}
                error={getFieldError('basicInfo')}
              />
            </Grid>

            {/* Product Image Section */}
            <Grid size={{ xs: 12 }}>
              <ProductImage
                data={formData.images}
                onChange={data => handleSectionChange('images', data)}
                error={getFieldError('images')}
              />
            </Grid>

            {/* Product Variants Section */}
            <Grid size={{ xs: 12 }}>
              <ProductVariants
                data={formData.variants}
                onChange={data => handleSectionChange('variants', data)}
                error={getFieldError('variants')}
              />
            </Grid>

            {/* Product Inventory Section */}
            <Grid size={{ xs: 12 }}>
              <ProductInventory
                data={formData.inventory}
                onChange={data => handleSectionChange('inventory', data)}
                error={getFieldError('inventory')}
              />
            </Grid>
          </Grid>
        </Grid>

        {/* Right Column - Sidebar Sections */}
        <Grid size={{ xs: 12, md: 4 }}>
          <Grid container spacing={6}>
            {/* Product Pricing Section */}
            <Grid size={{ xs: 12 }}>
              <ProductPricing
                data={formData.pricing}
                onChange={data => handleSectionChange('pricing', data)}
                error={getFieldError('pricing')}
              />
            </Grid>

            {/* Product Organize Section */}
            <Grid size={{ xs: 12 }}>
              <ProductOrganize
                data={formData.organize}
                onChange={data => handleSectionChange('organize', data)}
                error={getFieldError('organize')}
              />
            </Grid>
          </Grid>
        </Grid>
      </Grid>

      {/* Form Actions */}
      <FormActions
        isLoading={isSubmitting || isLoading}
        submitLabel={submitLabel}
        onCancel={onCancel}
        onSaveDraft={handleSaveDraft}
      />
    </form>
  )
}

export default ProductAddForm
\\\

---

## Sub-Components with Enhanced Props

### **ProductInformation Section**

\\\	ypescript
interface ProductInformationProps {
  data: ProductBasicInfo
  onChange: (data: Partial<ProductBasicInfo>) => void
  error?: string
  disabled?: boolean
  required?: boolean
}

const ProductInformation: React.FC<ProductInformationProps> = ({
  data,
  onChange,
  error,
  disabled = false,
  required = true
}) => {
  return (
    <Card error={!!error}>
      <CardHeader title='Product Information' />
      <CardContent>
        {error && <ErrorMessage message={error} />}
        
        <Grid container spacing={6}>
          <Grid size={{ xs: 12 }}>
            <CustomTextField
              fullWidth
              label='Product Name'
              placeholder='iPhone 14'
              value={data.productName}
              onChange={e => onChange({ productName: e.target.value })}
              error={!!error}
              required={required}
              disabled={disabled}
            />
          </Grid>
          {/* ... other fields ... */}
        </Grid>
      </CardContent>
    </Card>
  )
}
\\\

### **ProductImage Section**

\\\	ypescript
interface ProductImageProps {
  data: ProductImageData
  onChange: (data: Partial<ProductImageData>) => void
  error?: string
  maxFiles?: number
  maxSize?: number
}

const ProductImage: React.FC<ProductImageProps> = ({
  data,
  onChange,
  error,
  maxFiles = 10,
  maxSize = 5242880 // 5MB
}) => {
  const handleDrop = (acceptedFiles: File[]) => {
    if (data.files.length + acceptedFiles.length > maxFiles) {
      // Show error: too many files
      return
    }

    const validFiles = acceptedFiles.filter(f => f.size <= maxSize)
    onChange({ files: [...data.files, ...validFiles] })
  }

  return (
    <Card error={!!error}>
      <CardHeader title='Product Image' />
      <CardContent>
        {error && <ErrorMessage message={error} />}
        
        <Dropzone onDrop={handleDrop}>
          {/* Dropzone UI */}
        </Dropzone>

        {data.files.length > 0 && (
          <FileList
            files={data.files}
            onRemove={file => {
              onChange({
                files: data.files.filter(f => f.name !== file.name)
              })
            }}
          />
        )}
      </CardContent>
    </Card>
  )
}
\\\

### **ProductVariants Section**

\\\	ypescript
interface ProductVariantsProps {
  data: ProductVariant[]
  onChange: (data: ProductVariant[]) => void
  error?: string
}

const ProductVariants: React.FC<ProductVariantsProps> = ({
  data,
  onChange,
  error
}) => {
  const addVariant = () => {
    const newVariant: ProductVariant = {
      id: generateId(),
      optionType: 'Size',
      optionValue: ''
    }
    onChange([...data, newVariant])
  }

  const removeVariant = (id: string) => {
    onChange(data.filter(v => v.id !== id))
  }

  const updateVariant = (id: string, updates: Partial<ProductVariant>) => {
    onChange(
      data.map(v => (v.id === id ? { ...v, ...updates } : v))
    )
  }

  return (
    <Card error={!!error}>
      <CardHeader title='Product Variants' />
      <CardContent>
        {error && <ErrorMessage message={error} />}

        <Grid container spacing={6}>
          {data.map((variant, index) => (
            <VariantRow
              key={variant.id}
              variant={variant}
              onUpdate={updates => updateVariant(variant.id, updates)}
              onRemove={() => removeVariant(variant.id)}
              index={index}
            />
          ))}

          <Grid size={{ xs: 12 }}>
            <Button
              variant='contained'
              startIcon={<i className='tabler-plus' />}
              onClick={addVariant}
            >
              Add Another Option
            </Button>
          </Grid>
        </Grid>
      </CardContent>
    </Card>
  )
}
\\\

### **ProductPricing Section**

\\\	ypescript
interface ProductPricingProps {
  data: ProductPricingData
  onChange: (data: Partial<ProductPricingData>) => void
  error?: string
  currency?: string
}

const ProductPricing: React.FC<ProductPricingProps> = ({
  data,
  onChange,
  error,
  currency = 'USD'
}) => {
  const handlePriceChange = (field: string, value: string) => {
    // Validate price format
    if (!isValidPrice(value)) return

    onChange({ [field]: value })
  }

  const discountPercentage = calculateDiscount(data.basePrice, data.discountedPrice)

  return (
    <Card error={!!error}>
      <CardHeader title='Pricing' />
      <CardContent>
        {error && <ErrorMessage message={error} />}

        <Form>
          <CustomTextField
            fullWidth
            label='Base Price'
            placeholder={formatPrice(0, currency)}
            value={data.basePrice}
            onChange={e => handlePriceChange('basePrice', e.target.value)}
            startAdornment={<InputAdornment position='start'>{currency}</InputAdornment>}
          />

          <CustomTextField
            fullWidth
            label='Discounted Price'
            placeholder={formatPrice(0, currency)}
            value={data.discountedPrice}
            onChange={e => handlePriceChange('discountedPrice', e.target.value)}
            helperText={discountPercentage > 0 ? \\% discount\ : ''}
            startAdornment={<InputAdornment position='start'>{currency}</InputAdornment>}
          />

          {/* Additional pricing fields */}
        </Form>
      </CardContent>
    </Card>
  )
}
\\\

### **ProductOrganize Section**

\\\	ypescript
interface ProductOrganizeProps {
  data: ProductOrganizeData
  onChange: (data: Partial<ProductOrganizeData>) => void
  error?: string
  onAddCategory?: (category: string) => Promise<void>
}

const ProductOrganize: React.FC<ProductOrganizeProps> = ({
  data,
  onChange,
  error,
  onAddCategory
}) => {
  const [isAddingCategory, setIsAddingCategory] = useState(false)

  const handleAddCategory = async () => {
    if (onAddCategory) {
      setIsAddingCategory(true)
      try {
        await onAddCategory(data.category)
      } finally {
        setIsAddingCategory(false)
      }
    }
  }

  return (
    <Card error={!!error}>
      <CardHeader title='Organize' />
      <CardContent>
        {error && <ErrorMessage message={error} />}

        <form className='flex flex-col gap-6'>
          <VendorSelect
            value={data.vendor}
            onChange={vendor => onChange({ vendor })}
          />

          <div className='flex items-end gap-4'>
            <CategorySelect
              value={data.category}
              onChange={category => onChange({ category })}
              disabled={isAddingCategory}
            />
            <CustomIconButton
              variant='tonal'
              color='primary'
              onClick={handleAddCategory}
              loading={isAddingCategory}
            >
              <i className='tabler-plus' />
            </CustomIconButton>
          </div>

          {/* Collection, Status, Tags fields */}
        </form>
      </CardContent>
    </Card>
  )
}
\\\

---

## Server Action Integration Pattern

\\\	ypescript
// actions/productActions.ts
'use server'

import { db } from '@/lib/db'
import { revalidatePath } from 'next/cache'

export async function createProduct(
  data: ProductFormData
): Promise<{ success: boolean; error?: string; productId?: number }> {
  try {
    // Validate on server
    const validation = validateProductData(data)
    if (!validation.valid) {
      return { success: false, error: validation.error }
    }

    // Process images (upload to cloud storage, etc.)
    const imageUrls = await processImages(data.images.files)

    // Create product in database
    const product = await db.product.create({
      data: {
        name: data.basicInfo.productName,
        sku: data.basicInfo.sku,
        barcode: data.basicInfo.barcode,
        description: data.basicInfo.description,
        images: imageUrls,
        variants: data.variants,
        basePrice: parseFloat(data.pricing.basePrice),
        discountedPrice: parseFloat(data.pricing.discountedPrice),
        taxable: data.pricing.chargeTax,
        inStock: data.pricing.inStock,
        vendor: data.organize.vendor,
        category: data.organize.category,
        collection: data.organize.collection,
        status: data.organize.status,
        tags: data.organize.tags
      }
    })

    // Revalidate products list page
    revalidatePath('/apps/ecommerce/products/list')

    return { success: true, productId: product.id }
  } catch (error) {
    console.error('Product creation error:', error)
    return {
      success: false,
      error: error instanceof Error ? error.message : 'Failed to create product'
    }
  }
}

export async function updateProduct(
  productId: number,
  data: ProductFormData
): Promise<{ success: boolean; error?: string }> {
  try {
    // Similar validation and processing...
    
    const product = await db.product.update({
      where: { id: productId },
      data: {
        // Map form data to database fields
      }
    })

    revalidatePath('/apps/ecommerce/products/list')
    revalidatePath(\/apps/ecommerce/products/\\)

    return { success: true }
  } catch (error) {
    return {
      success: false,
      error: error instanceof Error ? error.message : 'Failed to update product'
    }
  }
}

export async function saveDraft(
  data: ProductFormData
): Promise<{ success: boolean; draftId?: number }> {
  try {
    const draft = await db.productDraft.create({
      data: {
        formData: data,
        savedAt: new Date()
      }
    })

    return { success: true, draftId: draft.id }
  } catch (error) {
    return { success: false }
  }
}
\\\

---

## Usage Examples

### **Basic Usage (Client Component)**

\\\	ypescript
'use client'

import { useRouter } from 'next/navigation'
import ProductAddForm from '@components/ProductAddForm'
import { createProduct } from '@/app/actions/productActions'

export function ProductAddPage() {
  const router = useRouter()

  return (
    <ProductAddForm
      submitAction={createProduct}
      onCancel={() => router.back()}
      submitLabel='Create Product'
    />
  )
}
\\\

### **With Initial Data (Edit Mode)**

\\\	ypescript
'use client'

export function ProductEditPage({ product }: { product: ProductFormData }) {
  const router = useRouter()

  return (
    <ProductAddForm
      initialData={product}
      submitAction={async (data) => {
        const result = await updateProduct(product.id, data)
        if (result.success) {
          router.push('/apps/ecommerce/products/list')
        }
        return result
      }}
      onCancel={() => router.back()}
      submitLabel='Update Product'
      onSaveDraft={async (data) => {
        await saveDraft(data)
      }}
    />
  )
}
\\\

### **With Custom Validation**

\\\	ypescript
'use client'

const validateProduct = (data: ProductFormData): FormValidationError[] => {
  const errors: FormValidationError[] = []

  // Custom validation rules
  if (data.basicInfo.productName.length < 3) {
    errors.push({
      field: 'basicInfo.productName',
      message: 'Product name must be at least 3 characters'
    })
  }

  if (data.variants.length === 0) {
    errors.push({
      field: 'variants',
      message: 'At least one variant is required'
    })
  }

  if (parseFloat(data.pricing.discountedPrice) > parseFloat(data.pricing.basePrice)) {
    errors.push({
      field: 'pricing.discountedPrice',
      message: 'Discounted price cannot be higher than base price'
    })
  }

  return errors
}

export function ProductAddPage() {
  return (
    <ProductAddForm
      submitAction={createProduct}
      submitLabel='Create Product'
    />
  )
}
\\\

---

## Reusable Patterns

### **Similar Forms Using This Component**

This component pattern can be adapted for:

1. **Order Creation Form**
   - Order items management
   - Customer selection
   - Shipping configuration
   - Payment details

2. **Customer Management Form**
   - Basic customer info
   - Contact details
   - Address management
   - Preferences

3. **Invoice Creation Form**
   - Invoice items
   - Tax calculations
   - Payment terms
   - Delivery method

---

## Utilities and Helpers

\\\	ypescript
// utils/formValidation.ts
export function validateProductData(data: ProductFormData): {
  valid: boolean
  error?: string
} {
  // Comprehensive validation logic
}

export function calculateDiscount(
  basePrice: string,
  discountedPrice: string
): number {
  const base = parseFloat(basePrice)
  const discounted = parseFloat(discountedPrice)
  if (base === 0) return 0
  return Math.round(((base - discounted) / base) * 100)
}

export function formatPrice(price: number, currency: string): string {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency
  }).format(price)
}

export function isValidPrice(value: string): boolean {
  return /^\d+(\.\d{1,2})?$/.test(value)
}

export function generateId(): string {
  return \\_\\
}
\\\

---

## Advanced Features (Optional Extensions)

### **Auto-Save Draft**

\\\	ypescript
const ProductAddFormWithAutoSave = (props: ProductAddFormProps) => {
  const [isSaving, setIsSaving] = useState(false)

  useEffect(() => {
    const timeout = setTimeout(async () => {
      if (props.onSaveDraft && formData) {
        setIsSaving(true)
        try {
          await props.onSaveDraft(formData)
        } finally {
          setIsSaving(false)
        }
      }
    }, 5000) // Auto-save every 5 seconds

    return () => clearTimeout(timeout)
  }, [formData])

  return (
    <>
      <ProductAddForm {...props} />
      {isSaving && <AutoSaveIndicator />}
    </>
  )
}
\\\

### **Multi-Step Wizard**

\\\	ypescript
const ProductAddWizard = (props: ProductAddFormProps) => {
  const [currentStep, setCurrentStep] = useState(0)

  const steps = [
    { title: 'Basic Info', component: ProductInformation },
    { title: 'Images', component: ProductImage },
    { title: 'Variants', component: ProductVariants },
    { title: 'Pricing', component: ProductPricing },
    { title: 'Organize', component: ProductOrganize }
  ]

  return (
    <Stepper activeStep={currentStep}>
      {steps.map((step, index) => (
        <Step key={index}>
          <StepLabel>{step.title}</StepLabel>
        </Step>
      ))}
    </Stepper>
  )
}
\\\

---

## Testing Patterns

\\\	ypescript
import { render, screen, fireEvent, waitFor } from '@testing-library/react'
import ProductAddForm from '@components/ProductAddForm'

describe('ProductAddForm', () => {
  it('renders all form sections', () => {
    render(<ProductAddForm />)
    expect(screen.getByText('Product Information')).toBeInTheDocument()
    expect(screen.getByText('Product Image')).toBeInTheDocument()
    expect(screen.getByText('Pricing')).toBeInTheDocument()
  })

  it('validates required fields', async () => {
    const { getByText } = render(<ProductAddForm />)
    
    fireEvent.click(getByText('Publish Product'))
    
    await waitFor(() => {
      expect(screen.getByText('Product name is required')).toBeInTheDocument()
    })
  })

  it('submits form with valid data', async () => {
    const submitAction = jest.fn()
    const { getByText, getByPlaceholderText } = render(
      <ProductAddForm submitAction={submitAction} />
    )

    // Fill form fields
    fireEvent.change(getByPlaceholderText('Product Name'), {
      target: { value: 'Test Product' }
    })
    fireEvent.change(getByPlaceholderText('Enter Base Price'), {
      target: { value: '99.99' }
    })

    fireEvent.click(getByText('Publish Product'))

    await waitFor(() => {
      expect(submitAction).toHaveBeenCalled()
    })
  })
})
\\\

---

## Performance Considerations

1. **Memoization**: Use React.memo for sub-components to prevent unnecessary re-renders
2. **Lazy Loading**: Load file upload functionality only when needed
3. **Debouncing**: Debounce field changes for auto-save functionality
4. **Image Optimization**: Compress and resize images before upload
5. **Form Validation**: Validate on change with debouncing for better UX

---

## Accessibility Features

- All form fields have associated labels
- Error messages linked to form fields via aria-describedby
- Keyboard navigation support for all controls
- Loading states announced via aria-busy
- Color + text for error states (not color alone)
- Proper heading hierarchy
- Form validation results in focus management

---

*Last Updated: Phase 3 Ecommerce & Forms, Task kb-79w.3.3*
*File Format: Markdown Snapshot (Component Documentation)*
*Implementation: React 19 + TypeScript + Next.js 15 + Material-UI*