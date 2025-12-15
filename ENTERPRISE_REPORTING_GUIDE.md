# Laravel Enterprise Reporting Setup Guide

## ✅ Installation Complete!

Your Laravel application is now configured with **Laravel-Native Enterprise Reporting** using DomPDF and Laravel Excel.

---

## 📋 What's Installed

1. **DomPDF** - Professional PDF generation with custom templates
2. **Laravel Excel** - Export data to Excel spreadsheets
3. **Report Service** - `App\Services\ReportService.php`
4. **Report Controller** - `App\Http\Controllers\ReportController.php`
5. **Report UI** - Professional report generator interface
6. **PDF Templates** - Custom Blade templates for Purchase Order and Inventory reports

---

## 🚀 How to Use

### Step 1: Access the Report Dashboard

**Navigate to:** http://localhost:8000/reports

### Step 2: Generate Reports

**Available Reports:**
- ✅ **Purchase Order Report** (PDF, Excel)
  - Select format (PDF or Excel)
  - Enter Purchase Order ID
  - Download instantly

- ✅ **Inventory Report** (PDF, Excel)
  - Click PDF or Excel button
  - Report includes all items with low stock alerts
  - Downloads automatically

- 🔒 **Project Summary** (Coming Soon)
- 🔒 **Employee Attendance** (Coming Soon)

### Step 3: Customize Report Templates

**PDF Templates Location:**
- Purchase Order: `resources/views/reports/purchase-order-pdf.blade.php`
- Inventory: `resources/views/reports/inventory-pdf.blade.php`

**Edit templates with:**
- HTML/CSS styling
- Blade templating
- Custom layouts
- Company branding

---

## 📁 Project Structure

```
ProjectManagement_MC/
├── app/
│   ├── Http/Controllers/
│   │   └── ReportController.php              # Report generation controller
│   └── Services/
│       └── ReportService.php                  # Report service logic
├── resources/views/reports/
│   ├── index.blade.php                        # Report generator UI
│   ├── purchase-order-pdf.blade.php           # Purchase Order PDF template
│   └── inventory-pdf.blade.php                # Inventory PDF template
└── storage/app/public/reports/                # Generated reports (auto-cleanup)
```

---

## 🎨 Creating New Reports

### Example: Create Project Report

1. **Create Blade Template**
   - File: `resources/views/reports/project-pdf.blade.php`
   - Copy structure from existing templates
   - Customize HTML/CSS

2. **Add Service Method**
   ```php
   public function generateProjectReport($projectId, $format = 'pdf')
   {
       $project = Project::with('status', 'client')->findOrFail($projectId);
       
       $data = [
           'project' => $project,
           'milestones' => $project->milestones,
       ];
       
       return $this->generatePDF('reports.project-pdf', $data, 'Project_Report');
   }
   ```

3. **Add Controller Method**
   ```php
   public function generateProject($id)
   {
       $filePath = $this->reportService->generateProjectReport($id);
       return Response::download($filePath);
   }
   ```

4. **Add Route**
   ```php
   Route::get('reports/project/{id}', [ReportController::class, 'generateProject'])
       ->name('reports.project')->middleware('auth');
   ```

5. **Add to UI** - Update `reports/index.blade.php` with new report card

---

## 🔧 Report Generation Methods

### From PHP Controller:

```php
use App\Services\ReportService;

public function generateReport(ReportService $reportService)
{
    // Generate Purchase Order PDF
    $pdfPath = $reportService->generatePurchaseOrderReport(1, 'pdf');
    
    // Generate Excel
    $excelPath = $reportService->generatePurchaseOrderReport(1, 'excel');
    
    // Generate Inventory Report
    $inventoryPath = $reportService->generateInventoryReport('pdf');
    
    return response()->download($pdfPath);
}
```

### Supported Export Formats:
- ✅ **PDF** - Professional documents with custom styling
- ✅ **Excel** - Spreadsheets with formatting and formulas

---

## 📊 Report Features

### Purchase Order Report:
- ✅ Professional header with PO number and date
- ✅ Supplier information box
- ✅ Status badges (Draft, Approved, Sent)
- ✅ Detailed items table with specifications
- ✅ Total quantity summary
- ✅ Signature sections (Prepared, Approved, Received)
- ✅ System branding with #87A96B color
- ✅ Page numbering and timestamps

### Inventory Report:
- ✅ Summary cards (Total Items, Stock Units, Low Stock Alerts)
- ✅ Low stock items highlighted section
- ✅ Complete inventory listing
- ✅ Visual indicators for low stock (yellow background, red text)
- ✅ Organized by categories
- ✅ Professional multi-page layout

---

## 🎯 Advanced Customization

### Adding Custom Styling:

**In Blade Template:**
```html
<style>
    .custom-section {
        background: #f8f9fa;
        padding: 15px;
        border-left: 4px solid #87A96B;
    }
</style>
```

### Adding Dynamic Content:

```blade
@if($purchaseOrder->Status === 'Approved')
    <div class="approved-stamp">APPROVED</div>
@endif
```

### Adding Charts (Future):

Install **Laravel Charts** for visual data representation:
```bash
composer require consoletvs/charts:6.*
```

### Multi-Language Support:

Add translations in Blade templates:
```blade
{{ __('reports.purchase_order_title') }}
```

---

## 🐛 Troubleshooting

### Error: "Class 'PDF' not found"
```bash
# Clear config cache
php artisan config:clear
php artisan cache:clear
```

### Error: "Excel download fails"
- Check Laravel Excel is installed
- Verify storage permissions
- Check disk space

### Error: "PDF styling not working"
- Use inline CSS (external CSS not supported in DomPDF)
- Avoid complex CSS3 features
- Use web-safe fonts

### Storage Permission Issues
```powershell
# Windows - Run as Administrator
icacls storage /grant Users:F /T
```

### Reports Not Generating
1. Check `storage/app/public/reports` folder exists
2. Verify database connections
3. Check Laravel logs: `storage/logs/laravel.log`
4. Clear cache: `php artisan cache:clear`

---

## 📚 Resources

- **DomPDF Documentation:** https://github.com/barryvdh/laravel-dompdf
- **Laravel Excel:** https://laravel-excel.com
- **Laravel Documentation:** https://laravel.com/docs
- **Blade Templates:** https://laravel.com/docs/blade

---

## 🎉 You're All Set!

**Test Your Setup:**

1. Run your Laravel app: `php artisan serve`
2. Visit: http://localhost:8000/reports
3. Click "Generate Report" on Purchase Order
4. Enter a valid POID (e.g., 1)
5. Select format (PDF or Excel)
6. Download and view your report!

**Need Help?**
- Check Laravel logs: `storage/logs/laravel.log`
- Verify database connection in `.env`
- Ensure storage permissions are correct
- Check that suppliers and items exist in database

---

## 🔄 Next Reports to Create

Suggested reports for your project management system:

1. **Project Progress Report**
   - Milestones completion percentage
   - Timeline vs. actual progress
   - Resource allocation

2. **Employee Attendance Report**
   - Daily/weekly/monthly attendance
   - Late arrivals and absences
   - Attendance by project

3. **Budget Report**
   - Project costs breakdown
   - Purchase orders summary
   - Expense tracking

4. **Supplier Performance Report**
   - Delivery times analysis
   - Order fulfillment rates
   - Quality metrics

5. **Equipment Utilization Report**
   - Equipment usage by project
   - Maintenance schedules
   - Incident tracking

---

## ⚡ Pro Tips

1. **Optimize PDFs** - Keep images under 1MB, use web-safe fonts
2. **Excel Formulas** - Add SUM(), AVERAGE() in Excel exports
3. **Custom Branding** - Add your company logo to PDF templates
4. **Scheduled Reports** - Use Laravel Task Scheduler for auto-generated reports
5. **Email Reports** - Integrate with Laravel Mail to send reports automatically

Happy Reporting! 🎊
