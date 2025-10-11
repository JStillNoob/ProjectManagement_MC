# On-Call Employees - Updated Structure

## 🎯 **On-Call Employee System Updated**

The on-call employee system has been completely updated to use the same modern address and salary structure as regular employees.

## 📊 **Updated Features**

### **🏠 Address Structure**
- **Before**: Single `address` text field
- **After**: Structured address fields:
  - `house_number` (optional)
  - `street` (optional)
  - `barangay` (required)
  - `city` (required)
  - `province` (required)
  - `postal_code` (optional)
  - `country` (default: Philippines)

### **💰 Salary Structure**
- **Before**: No salary fields
- **After**: Clean salary structure:
  - `base_salary` (decimal) - The actual salary amount
  - `salary_type` (enum) - Monthly/Daily/Hourly
  - Automatic monthly equivalent calculation

## 🎨 **Updated Views**

### **1. Create Form** (`resources/views/Admin/employees/oncall/create.blade.php`)
- **Address Section**: Structured address fields (house number, street, barangay, city, province, postal code)
- **Salary Section**: Base salary amount + salary type selection
- **Validation**: Proper validation for all new fields
- **Salary Type Priority**: Daily and Hourly options prioritized for on-call employees

### **2. Show View** (`resources/views/Admin/employees/oncall/show.blade.php`) - **NEW**
- **Complete Employee Details**: Personal, employment, salary, and contact information
- **Address Display**: Uses `$employee->full_address` accessor
- **Salary Display**: Shows formatted salary and monthly equivalent
- **Benefits Notice**: Clear indication that on-call employees are not eligible for benefits
- **QR Code Display**: Shows employee QR code if available

### **3. Edit View** (`resources/views/Admin/employees/oncall/edit.blade.php`) - **NEW**
- **Full Edit Form**: All fields editable with current values pre-filled
- **Address Fields**: Structured address editing
- **Salary Fields**: Base salary and type editing
- **Image Upload**: Current photo display with option to change
- **Validation**: Proper validation for all fields

### **4. Index View** (`resources/views/Admin/employees/oncall/index.blade.php`)
- **Salary Column**: Added salary information display
- **Formatted Salary**: Shows formatted salary with type
- **Enhanced Display**: Better organization of employee information

## ⚙️ **Updated Controller**

### **OnCallEmployeeController**
- **Validation Rules**: Updated for new address and salary fields
- **Required Fields**: Barangay, city, province, base_salary, salary_type
- **Optional Fields**: House number, street, postal code, country
- **Store Method**: Handles new field structure
- **Update Method**: Handles new field structure

## 🎯 **Key Differences from Regular Employees**

### **Salary Type Priority**
- **On-Call**: Daily and Hourly options prioritized (more common for on-call work)
- **Regular**: Monthly option prioritized (more common for regular employment)

### **Benefits Eligibility**
- **On-Call**: Not eligible for benefits (clearly indicated in views)
- **Regular**: Eligible for benefits (can be assigned by admin)

### **Availability Field**
- **On-Call**: Required field with specific options (Weekdays Only, Weekends Only, Evenings, Flexible, 24/7)
- **Regular**: Optional field

## 💡 **Usage Examples**

### **Creating an On-Call Employee**
```php
$employee = Employee::create([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'barangay' => 'Barangay 1',
    'city' => 'Manila',
    'province' => 'Metro Manila',
    'position' => 'On-Call Developer',
    'base_salary' => 500.00,
    'salary_type' => 'Daily',
    'availability' => 'Flexible',
    'contact_number' => '+1234567890',
    'EmployeeTypeID' => 2 // On-call employee type
]);

// Access formatted data
echo $employee->full_address; // "Barangay 1, Manila, Metro Manila, Philippines"
echo $employee->formatted_salary; // "₱500.00 Daily"
echo $employee->monthly_salary; // 11000 (500 * 22 days)
```

### **Display in Views**
```php
// Full address
{{ $employee->full_address }}

// Formatted salary
{{ $employee->formatted_salary }}

// Monthly equivalent for payroll
{{ number_format($employee->monthly_salary, 2) }}
```

## 🔄 **Migration Applied**
- **Migration**: `2025_10_08_172142_update_employees_table_salary_and_address_structure.php`
- **Status**: ✅ Successfully executed
- **Result**: On-call employees now use the same modern structure as regular employees

## ✅ **Benefits of Updated Structure**

### **Consistency**
1. **Unified Structure**: Same address and salary fields for all employee types
2. **Consistent Validation**: Same validation rules across employee types
3. **Standardized Display**: Same formatting and display methods

### **Flexibility**
1. **Multiple Salary Types**: Supports Monthly, Daily, and Hourly salaries
2. **Structured Address**: Easy to query and filter by location
3. **Automatic Calculations**: Monthly equivalent calculated automatically

### **User Experience**
1. **Clear Forms**: Well-organized sections for different information types
2. **Helpful Validation**: Clear error messages and field requirements
3. **Professional Display**: Clean, organized views for all employee information

## 🚀 **On-Call Employee Workflow**

### **1. Creation Process**
1. Fill out personal information (name, birthday, age)
2. Enter structured address information
3. Set employment details (position, start date)
4. Configure salary (base amount + type)
5. Set availability preferences
6. Add contact information
7. Upload photo (optional)

### **2. Management Features**
- **View Details**: Complete employee information display
- **Edit Information**: Update any field including address and salary
- **Delete Employee**: Soft delete with confirmation
- **List View**: Overview of all on-call employees with salary information

### **3. Payroll Integration**
- **Monthly Equivalent**: Automatic calculation for payroll systems
- **Salary Type**: Clear indication of payment structure
- **Benefits Status**: Clear indication that benefits are not applicable

The on-call employee system is now fully modernized and consistent with the regular employee system!

