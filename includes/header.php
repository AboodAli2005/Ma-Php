<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>LUXE Professional</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "on-tertiary-container": "#0c9488",
                        "surface-container-highest": "#e0e3e5",
                        "surface-container-lowest": "#ffffff",
                        "tertiary-container": "#00201d",
                        "tertiary-fixed-dim": "#6bd8cb",
                        "on-secondary-fixed-variant": "#38485d",
                        "inverse-primary": "#bec6e0",
                        "on-primary": "#ffffff",
                        "surface-tint": "#565e74",
                        "inverse-surface": "#2d3133",
                        "on-tertiary-fixed": "#00201d",
                        "surface-bright": "#f7f9fb",
                        "primary-fixed-dim": "#bec6e0",
                        "on-secondary": "#ffffff",
                        "on-error-container": "#93000a",
                        "on-surface-variant": "#45464d",
                        "secondary-fixed": "#d3e4fe",
                        "on-error": "#ffffff",
                        "inverse-on-surface": "#eff1f3",
                        "on-secondary-fixed": "#0b1c30",
                        "on-primary-fixed-variant": "#3f465c",
                        "secondary-container": "#d0e1fb",
                        "surface-variant": "#e0e3e5",
                        "error": "#ba1a1a",
                        "tertiary": "#000000",
                        "primary-container": "#131b2e",
                        "surface-container": "#eceef0",
                        "on-primary-container": "#7c839b",
                        "tertiary-fixed": "#89f5e7",
                        "error-container": "#ffdad6",
                        "surface-container-low": "#f2f4f6",
                        "on-surface": "#191c1e",
                        "primary-fixed": "#dae2fd",
                        "on-tertiary-fixed-variant": "#005049",
                        "outline-variant": "#c6c6cd",
                        "secondary": "#505f76",
                        "surface-dim": "#d8dadc",
                        "on-tertiary": "#ffffff",
                        "on-background": "#191c1e",
                        "secondary-fixed-dim": "#b7c8e1",
                        "primary": "#000000",
                        "surface-container-high": "#e6e8ea",
                        "background": "#f7f9fb",
                        "on-primary-fixed": "#131b2e",
                        "on-secondary-container": "#54647a",
                        "outline": "#76777d",
                        "surface": "#f7f9fb"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "xl": "64px",
                        "margin-mobile": "16px",
                        "md": "24px",
                        "lg": "40px",
                        "margin-desktop": "32px",
                        "base": "4px",
                        "xs": "8px",
                        "container-max": "1280px",
                        "gutter": "24px",
                        "sm": "16px"
                    },
                    "fontFamily": {
                        "headline-xl-mobile": ["Inter"],
                        "headline-sm": ["Inter"],
                        "headline-lg": ["Inter"],
                        "headline-xl": ["Inter"],
                        "body-sm": ["Inter"],
                        "label-md": ["Inter"],
                        "body-md": ["Inter"],
                        "label-sm": ["Inter"],
                        "headline-md": ["Inter"],
                        "body-lg": ["Inter"]
                    },
                    "fontSize": {
                        "headline-xl-mobile": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "headline-sm": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
                        "headline-lg": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600" }],
                        "headline-xl": ["48px", { "lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "body-sm": ["14px", { "lineHeight": "20px", "fontWeight": "400" }],
                        "label-md": ["14px", { "lineHeight": "20px", "letterSpacing": "0.05em", "fontWeight": "500" }],
                        "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "label-sm": ["12px", { "lineHeight": "16px", "fontWeight": "600" }],
                        "headline-md": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
                        "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }]
                    }
                }
            }
        }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-background text-on-background font-body-md min-h-screen flex flex-col">
<!-- TopNavBar Component -->
<header class="bg-surface dark:bg-inverse-surface text-primary dark:text-inverse-primary font-body-md text-body-md docked full-width top-0 sticky border-b border-outline-variant dark:border-outline shadow-sm z-50">
<div class="flex justify-between items-center w-full px-margin-desktop max-w-container-max mx-auto h-16">
<!-- Brand -->
<a class="font-headline-md text-headline-md font-bold text-primary dark:text-inverse-primary" href="index.php">LUXE</a>
<!-- Navigation Links -->
<nav class="hidden md:flex gap-md h-full items-center">
<a class="text-primary dark:text-inverse-primary border-b-2 border-primary dark:border-inverse-primary pb-1 h-full flex items-center mt-[2px]" href="index.php">Home</a>
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors h-full flex items-center" href="products.php">Products</a>
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors h-full flex items-center" href="cart.php">Cart</a>
<a class="text-secondary dark:text-secondary-fixed-dim hover:text-primary dark:hover:text-inverse-primary transition-colors h-full flex items-center" href="contact.php">Contact</a>
</nav>
<!-- Search & Actions -->
<div class="flex items-center gap-md">
<!-- Search Bar -->
<div class="relative hidden sm:block">
<form action="products.php" method="GET">
<input name="search" class="border border-outline-variant rounded bg-surface px-3 py-1 text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/10 transition-all w-48" placeholder="Search..." type="text"/>
<button type="submit" class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</button>
</form>
</div>
<!-- Icons & Sign In -->
<div class="flex items-center gap-xs">
<a href="cart.php" class="p-2 hover:bg-surface-container-low dark:hover:bg-surface-container-highest transition-all duration-200 rounded-full cursor-pointer active:opacity-70 flex items-center justify-center">
<span class="material-symbols-outlined" data-icon="shopping_bag">shopping_bag</span>
</a>
<?php if(isset($_SESSION['user_id'])): ?>
<a href="profile.php" class="p-2 hover:bg-surface-container-low dark:hover:bg-surface-container-highest transition-all duration-200 rounded-full cursor-pointer active:opacity-70 flex items-center justify-center">
<span class="material-symbols-outlined" data-icon="account_circle">account_circle</span>
</a>
<a class="font-label-md text-label-md text-primary ml-2 hover:underline" href="logout.php">Logout</a>
<?php else: ?>
<a class="font-label-md text-label-md text-primary ml-2 hover:underline" href="login.php">Sign In</a>
<?php endif; ?>
</div>
</div>
</div>
</header>
<main class="flex-grow">
