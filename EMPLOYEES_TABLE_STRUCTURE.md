# Employees Table Structure - Current Implementation

## 📊 **Current Table Schema**

The `employees` table has been updated and cleaned up to work with the new benefits management system.

### **Core Fields**

| Field Name | Type | Constraints | Description |
|------------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Unique employee identifier |
| `first_name` | varchar(255) | Required | Employee's first name |
| `middle_name` | varchar(255) | Nullable | Employee's middle name |
| `last_name` | varchar(255) | Required | Employee's last name |
| `birthday` | date | Required | Employee's date of birth |
| `age` | integer | Required, Min: 18, Max: 100 | Employee's age |
| `address` | text | Required | Employee's address |
| `status` | enum | Required, Default: 'Active' | Employment status (Active/Inactive) |
| `position` | varchar(255) | Required | Employee's job position |
| `start_date` | date | Required | Employment start date |
| `image_name` | varchar(255) | Nullable | Profile image filename |
| `qr_code` | varchar(255) | Nullable | QR code URL or path |
| `flag_deleted` | boolean | Default: 0 | Soft delete flag |
| `created_at` | timestamp | Auto | Record creation timestamp |
| `updated_at` | timestamp | Auto | Record update timestamp |

### **Employment Type & Salary Fields**

| Field Name | Type | Constraints | Description |
|------------|------|-------------|-------------|
| `EmployeeTypeID` | bigint | Foreign Key, Nullable | References employee_types table |
| `daily_salary` | decimal(10,2) | Nullable | Daily salary amount |
| `hourly_rate` | decimal(10,2) | Nullable | Hourly wage rate |
| `monthly_salary` | decimal(10,2) | Nullable | Monthly salary amount |

### **Contact Information Fields**

| Field Name | Type | Constraints | Description |
|------------|------|-------------|-------------|
| `availability` | varchar(255) | Nullable | Employee availability status |
| `contact_number` | varchar(255) | Nullable | Primary contact number |
| `emergency_contact` | varchar(255) | Nullable | Emergency contact name |
| `emergency_phone` | varchar(255) | Nullable | Emergency contact number |

## 🔗 **Foreign Key Relationships**

### **EmployeeType Relationship**
```sql
FOREIGN KEY (EmployeeTypeID) REFERENCES employee_types(EmployeeTypeID) ON DELETE SET NULL
```

### **Benefits Relationship (Many-to-Many)**
```sql
-- Through employee_benefits junction table
employees.id → employee_benefits.EmployeeID
benefits.BenefitID → employee_benefits.BenefitID
```

## 🗑️ **Removed Legacy Fields**

The following fields have been **removed** from the employees table as they are now handled by the proper benefits system:

- ❌ `benefits` (text) - Now handled by `employee_benefits` table
- ❌ `health_insurance` (varchar) - Now handled by `benefits` table
- ❌ `retirement_plan` (varchar) - Now handled by `benefits` table
- ❌ `vacation_days` (integer) - Now handled by `benefits` table
- ❌ `sick_days` (integer) - Now handled by `benefits` table

## 📋 **Model Configuration**

### **Employee Model Fillable Fields**
```php
protected $fillable = [
    'first_name',
    'middle_name', 
    'last_name',
    'birthday',
    'age',
    'address',
    'status',
    'position',
    'start_date',
    'image_name',
    'qr_code',
    'flag_deleted',
    'EmployeeTypeID',
    'daily_salary',
    'hourly_rate',
    'monthly_salary',
    'availability',
    'contact_number',
    'emergency_contact',
    'emergency_phone'
];
```

### **Model Casts**
```php
protected $casts = [
    'birthday' => 'date',
    'start_date' => 'date',
    'flag_deleted' => 'boolean',
    'daily_salary' => 'decimal:2',
    'hourly_rate' => 'decimal:2',
    'monthly_salary' => 'decimal:2',
];
```

## 🎯 **Benefits Management Integration**

### **How Benefits Work Now**
1. **Employee Creation**: Employee is created with basic information
2. **Benefits Assignment**: Admin manually assigns benefits through the benefits management interface
3. **Benefits Storage**: Benefits are stored in the `employee_benefits` junction table
4. **Benefits Types**: All benefit types are defined in the `benefits` table

### **Benefits Access Methods**
```php
// Get employee's current benefits
$employee->getCurrentBenefits()

// Check if employee is eligible for benefits
$employee->isEligibleForBenefits()

// Get benefits through relationship
$employee->benefits
$employee->employeeBenefits
```

## 🔄 **Migration History**

1. **Initial Creation**: `2025_09_11_234158_create_employees_table.php`
2. **QR Code Addition**: `2025_09_19_030352_add_qr_code_to_employees_table.php`
3. **Salary Fields**: `2025_10_03_214610_add_daily_salary_to_employees_table.php`
4. **Foreign Keys**: `2025_10_03_205710_add_foreign_keys_to_existing_tables.php`
5. **Legacy Cleanup**: `2025_10_08_171440_clean_up_employees_table_remove_legacy_benefit_fields.php`

## ✅ **Current Status**

The employees table is now:
- ✅ **Clean and normalized** - No redundant benefit fields
- ✅ **Properly related** - Connected to employee_types and benefits systems
- ✅ **Flexible** - Supports different employee types and salary structures
- ✅ **Scalable** - Easy to add new fields or relationships
- ✅ **Integrated** - Works seamlessly with the benefits management system

## 🚀 **Usage Examples**

### **Creating an Employee**
```php
$employee = Employee::create([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'birthday' => '1990-01-01',
    'age' => 34,
    'address' => '123 Main St',
    'position' => 'Software Developer',
    'start_date' => '2024-01-01',
    'EmployeeTypeID' => 1, // Regular employee
    'monthly_salary' => 50000.00,
    'contact_number' => '+1234567890'
]);
```

### **Accessing Benefits**
```php
// Check if employee has benefits
if ($employee->isEligibleForBenefits()) {
    $benefits = $employee->getCurrentBenefits();
    // Process benefits...
}
```

The employees table is now fully optimized and integrated with the modern benefits management system!

