# Role-Based Permissions Implementation (Admin/User)

## Plan Steps:
1. [x] Update app/Models/User.php - Add 'role' to $fillable
2. [x] Create app/Http/Controllers/AuthController.php - Login, logout, register handlers
3. [x] Update routes/web.php - Fix auth routes to controller, add /diem-danh GET for users
4. [x] Update resources/views/register.blade.php - Remove mssv field, align with User model (already done)
5. [x] Run `php artisan db:seed` to ensure admin user
6. [x] Fixed routes middleware syntax error
7. [x] Task complete - permissions implemented

Current progress tracked here.
