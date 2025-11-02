# Simple PHP Backend for `admin.php` (Student-Style)

This is a minimal backend to save the **Baptism** and **Wedding** certificate forms from your `admin.php`.  
It keeps things very simple: when a form is submitted, we store all fields as a JSON blob in a single table, plus a guessed display name. There's also a tiny list page and a CSV export.

## Files

- `db.php` – database connection (mysqli). Edit credentials here or via environment variables.
- `submit_common.php` – helper used by both form handlers.
- `submit_baptism.php` – POST target for the Baptism form in `admin.php`.
- `submit_wedding.php` – POST target for the Wedding form in `admin.php`.
- `view_submissions.php` – simple list/search page for saved entries.
- `export_csv.php` – download all (or filtered) submissions as CSV.
- `schema.sql` – SQL to create the database and table.

## Quick Start

1. **Copy files** into the same folder as your `admin.php` (so the form `action="submit_baptism.php"` etc. works).
2. **Create the DB**: open MySQL (phpMyAdmin or CLI) and run the statements in `schema.sql`.
3. **Configure credentials**: edit `db.php` or set env vars `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`.
4. **Test it**:
   - Open `admin.php`, go to Certificates → Baptism or Wedding, fill in the form, and submit.
   - You should be redirected back to the page with `?saved=1&id=###`.
   - Visit `view_submissions.php` to see your saved records and export CSV if needed.

## Notes

- This is intentionally simple (student-style). It uses **one table** and saves the whole POST as JSON to avoid messing with lots of columns.
- Basic prepared statements are used, but there’s **no authentication** and **no advanced validation**. For production, add a login and stricter checks.
- If your form uses arrays (e.g., multiple sponsors), they are saved as arrays inside the JSON.
- You can tweak the "display name" logic in `submit_common.php` if you prefer a different name field.

## Optional: Link from your sidebar

Add a link somewhere in your `admin.php` to open the list page:
```html
<a href="view_submissions.php" class="btn btn-default">View Submissions</a>
```

Good luck! 🎓
