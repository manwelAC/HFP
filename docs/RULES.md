**Guide & Version for this Project template**
1. We are using Laravel ver "8.75"
2. Our php ver "7.3| 8.0"
3. Bootstrap for our styling

**Guide for making CRUD UI**
- Existing folders like nte_management, disciplinary, and incident_report ui are sample CRUD Ui that I did might as well follow the pattern for the styling.
- Always remember to use the layouts/front-app for creating the view so that plugins are no longer needed to be included in the blade file.
- If the Crud includes attachments do not include storage linking, just use the public/ folders for better file path finding.


**View Patterns:**
- Single main view per module (no separate create/edit views)
- Modal-based forms for create/edit operations
- AJAX-driven table data loading
- Bootstrap-based responsive layout

**Controllers:**
- Query Builder (DB facade) instead of Eloquent ORM
- Separate methods for: index, list, view, store, update, delete
- JSON responses for all AJAX operations

**Database:**
- No Eloquent models created for CRUD operations
- Minimal models (company_setting, employee_logged, role, User only)
- Uses raw DB queries with joins

### Route Structure Pattern:
```
{module}/index {entity}/list {entity}/view/{id} {entity}/store {entity}/update/{id} {entity}/delete/{id}
```

