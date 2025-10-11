# Updated Employees Table Structure

## 🎯 **Major Updates Applied**

The employees table has been completely restructured with proper salary and address fields.

## 📊 **New Table Schema**

### **Core Information Fields**
| Field Name | Type | Constraints | Description |
|------------|------|-------------|-------------|
| `id` | bigint | Primary Key, Auto Increment | Unique employee identifier |
| `first_name` | varchar(255) | Required | Employee's first name |
| `middle_name` | varchar(255) | Nullable | Employee's middle name |
| `last_name` | varchar(255) | Required | Employee's last name |
| `birthday` | date | Required | Employee's date of birth |
| `age` | integer | Required, Min: 18, Max: 100 | Employee's age |

### **Address Fields (NEW STRUCTURE)**
| Field Name | Type | Constraints | Description |
|------------|------|-------------|-------------|
| `house_number` | varchar(255) | Nullable | House/building number |
| `street` | varchar(255) | Nullable | Street name |
| `barangay` | varchar(255) | Required | Barangay name |
| `city` | varchar(255) | Required | City name |
| `province` | varchar(255) | Required | Province name |
| `postal_code` | varchar(10) | Nullable | Postal/ZIP code |
| `country` | varchar(255) | Default: 'Philippines' | Country name |

### **Employment & Salary Fields (UPDATED)**
| Field Name | Type | Constraints | Description |
|------------|------|-------------|-------------|
| `status` | enum | Required, Default: 'Active' | Employment status (Active/Inactive) |
| `position` | varchar(255) | Required | Employee's job position |
| `base_salary` | decimal(12,2) | Nullable | Base salary amount |
| `salary_type` | enum | Default: 'Monthly' | Salary type (Monthly/Daily/Hourly) |
| `start_date` | date | Required | Employment start date |

### **System Fields**
| Field Name | Type | Constraints | Description |
|------------|------|-------------|-------------|
| `image_name` | varchar(255) | Nullable | Profile image filename |
| `qr_code` | varchar(255) | Nullable | QR code URL or path |
| `flag_deleted` | boolean | Default: 0 | Soft delete flag |
| `EmployeeTypeID` | bigint | Foreign Key, Nullable | References employee_types table |
| `availability` | varchar(255) | Nullable | Employee availability status |
| `contact_number` | varchar(255) | Nullable | Primary contact number |
| `emergency_contact` | varchar(255) | Nullable | Emergency contact name |
| `emergency_phone` | varchar(255) | Nullable | Emergency contact number |
| `created_at` | timestamp | Auto | Record creation timestamp |
| `updated_at` | timestamp | Auto | Record update timestamp |

## 🔄 **Changes Made**

### **Removed Fields**
- ❌ `address` (text) - Replaced with structured address fields
- ❌ `daily_salary` (decimal) - Replaced with base_salary + salary_type
- ❌ `hourly_rate` (decimal) - Replaced with base_salary + salary_type
- ❌ `monthly_salary` (decimal) - Replaced with base_salary + salary_type

### **Added Fields**
- ✅ `house_number` - House/building number
- ✅ `street` - Street name
- ✅ `barangay` - Barangay (required)
- ✅ `city` - City (required)
- ✅ `province` - Province (required)
- ✅ `postal_code` - Postal code
- ✅ `country` - Country (default: Philippines)
- ✅ `base_salary` - Base salary amount
- ✅ `salary_type` - Salary type (Monthly/Daily/Hourly)

## 🎨 **Model Enhancements**

### **New Accessors**
```php
// Full address accessor
public function getFullAddressAttribute()
{
    $addressParts = array_filter([
        $this->house_number,
        $this->street,
        $this->barangay,
        $this->city,
        $this->province,
        $this->postal_code,
        $this->country
    ]);
    
    return implode(', ', $addressParts);
}

// Formatted salary accessor
public function getFormattedSalaryAttribute()
{
    if (!$this->base_salary) {
        return 'Not set';
    }
    
    return '₱' . number_format($this->base_salary, 2) . ' ' . $this->salary_type;
}

// Monthly equivalent salary calculation
public function getMonthlySalaryAttribute()
{
    if (!$this->base_salary) {
        return 0;
    }

    switch ($this->salary_type) {
        case 'Monthly':
            return $this->base_salary;
        case 'Daily':
            return $this->base_salary * 22; // 22 working days
        case 'Hourly':
            return $this->base_salary * 8 * 22; // 8 hours/day, 22 days
        default:
            return $this->base_salary;
    }
}
```

## 🎯 **Updated Fillable Fields**
```php
protected $fillable = [
    'first_name', 'middle_name', 'last_name',
    'birthday', 'age',
    'house_number', 'street', 'barangay', 'city', 'province', 'postal_code', 'country',
    'status', 'position', 'base_salary', 'salary_type', 'start_date',
    'image_name', 'qr_code', 'flag_deleted', 'EmployeeTypeID',
    'availability', 'contact_number', 'emergency_contact', 'emergency_phone'
];
```

## 🎨 **Updated Views**

### **Create Form** (`resources/views/Admin/employees/regular/create.blade.php`)
- **Address Section**: Structured address fields (house number, street, barangay, city, province, postal code)
- **Salary Section**: Base salary amount + salary type selection
- **Validation**: Proper validation for all new fields

### **Show View** (`resources/views/Admin/employees/regular/show.blade.php`)
- **Address Display**: Uses `$employee->full_address` accessor
- **Salary Display**: Shows formatted salary and monthly equivalent
- **Clean Layout**: Organized sections for different information types

## ⚙️ **Updated Controllers**

### **RegularEmployeeController**
- **Validation**: Updated validation rules for new fields
- **Required Fields**: Barangay, city, province, base_salary, salary_type
- **Optional Fields**: House number, street, postal code, country

### **EmployeeController**
- **Validation**: Updated validation rules for new fields
- **Flexible**: Base salary and salary type are optional for general employees

## 💡 **Benefits of New Structure**

### **Address Benefits**
1. **Structured Data**: Easy to query by city, province, barangay
2. **Standardized Format**: Consistent address format across the system
3. **Geographic Filtering**: Can filter employees by location
4. **Postal Integration**: Ready for postal code integration

### **Salary Benefits**
1. **Flexible Types**: Supports Monthly, Daily, and Hourly salaries
2. **Automatic Calculation**: Monthly equivalent calculated automatically
3. **Consistent Formatting**: Standardized salary display
4. **Payroll Ready**: Easy integration with payroll systems

## 🚀 **Usage Examples**

### **Creating an Employee with New Structure**
```php
$employee = Employee::create([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'birthday' => '1990-01-01',
    'age' => 34,
    'house_number' => '123',
    'street' => 'Main Street',
    'barangay' => 'Barangay 1',
    'city' => 'Manila',
    'province' => 'Metro Manila',
    'postal_code' => '1000',
    'country' => 'Philippines',
    'position' => 'Software Developer',
    'base_salary' => 50000.00,
    'salary_type' => 'Monthly',
    'start_date' => '2024-01-01',
    'EmployeeTypeID' => 1
]);

// Access formatted data
echo $employee->full_address; // "123, Main Street, Barangay 1, Manila, Metro Manila, 1000, Philippines"
echo $employee->formatted_salary; // "₱50,000.00 Monthly"
echo $employee->monthly_salary; // 50000 (monthly equivalent)
```

### **Address Display**
```php
// Full address
{{ $employee->full_address }}

// Individual components
{{ $employee->house_number }} {{ $employee->street }}
{{ $employee->barangay }}, {{ $employee->city }}
{{ $employee->province }} {{ $employee->postal_code }}
{{ $employee->country }}
```

### **Salary Display**
```php
// Formatted salary
{{ $employee->formatted_salary }}

// Monthly equivalent for payroll
{{ number_format($employee->monthly_salary, 2) }}
```

## ✅ **Migration Applied**
- **Migration**: `2025_10_08_172142_update_employees_table_salary_and_address_structure.php`
- **Status**: ✅ Successfully executed
- **Result**: Table structure updated with proper address and salary fields

The employees table is now properly structured with standardized address fields and flexible salary management!

