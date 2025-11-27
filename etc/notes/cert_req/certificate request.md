for admin dashboard:
- certificate request id (primary key)
- user id (foreign key references users table)
- contact
- email 
- **request_name** (request_firstname + request_middlename + request_lastname + request_suffix) **(if claiming for self)** or **owner_name** (owner_firstname + owner_middlename + owner_lastname + owner_suffix) **(if claiming for someone else)**
- certificate type (baptism, confirmation, first communion, wedding)
- certificate status (pending, declined, accepted)
- id_picture (path)
- receipt_picture (path)
- authorization_letter (path)
- created_at
- confirm (accept/decline)
- action (edit/delete)

---

Certificate Request Process
1. User submits form and required documents
2. Admin confirms and accepts the certificate request
3. Admin sets date for pick up
4. User receives notification for pick up date