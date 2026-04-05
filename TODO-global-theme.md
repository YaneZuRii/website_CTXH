# TODO: Global White + Green Theme (trắng + xanh lá #05cd99) cho TẤT CẢ files/pages

## [ ] Bước 1: Update :root variables & body text
- --primary: #05cd99
- --text-main: #065f46 (dark green)
- --primary-light: #ecfdf5
- Body color: var(--text-main)

## [ ] Bước 2: Sidebar green (hover/active)
- sidebar-menu a:hover, .active → var(--primary), background var(--primary-light)

## [ ] Bước 3: Table/Card hovers green
- .modern-table tr:hover → rgba(5,205,153,0.05)
- .modern-card:hover shadow → rgba(5,205,153,0.08)

## [ ] Bước 4: Auth/Login forms green
- .form-control-saas:focus → green border/shadow rgba(5,205,153,0.1)
- .btn-primary-saas → --primary, hover #04b386

## [ ] Bước 5: Consistent white glass everywhere
- All cards: white bg priority
- Test: login, dashboard, su-kien, diemdanh

## [ ] Bước 6: Hoàn thành Global Theme
