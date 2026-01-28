---
trigger: manual
---

# UI/UX Design Rules - e-service-app

## Brand Identity & Colors
- **Primary Color**: Always use **Amber/Orange** for primary actions, buttons, and highlights.
  - Tailwind classes: `amber-500` (main), `amber-600` (hover), `amber-700` (active).
  - Surface/Light Background: `bg-amber-50` or `bg-orange-50` for highlights.
- **Surface/Neutral**: 
  - Main Background: `bg-slate-50`.
  - Cards/Containers: `bg-white`.
  - Borders: `border-slate-200`.
- **Success**: `emerald-500`.
- **Error/Danger**: `rose-500`.
- **Warning**: `amber-400`.

## Component Standards
- **Buttons**:
  - Primary: `bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition-all duration-200`.
  - Secondary: `bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-medium py-2 px-4 rounded-lg`.
  - Danger: `bg-rose-500 hover:bg-rose-600 text-white font-semibold py-2 px-4 rounded-lg`.
- **Cards**: Use `bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden`. Headers should have a subtle bottom border.
- **Forms**:
  - Inputs: `border-slate-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 block w-full sm:text-sm`.
  - Labels: `block text-sm font-semibold text-slate-700 mb-1`.
  - Error Messages: `mt-1 text-xs text-rose-500`.
- **Modals**: Rounded corners `rounded-2xl`, smooth backdrop `bg-slate-900/50 backdrop-blur-sm`.

## Aesthetics & Premium Feel
- **Gradients**: Use subtle gradients for hero sections or status badges (e.g., `bg-gradient-to-r from-amber-500 to-orange-600`).
- **Typography**: 
  - Primary: `Figtree` (as per tailwind config).
  - Use `text-slate-900` for headings and `text-slate-600` for body text.
- **Shadows**: Prefer `shadow-sm` for cards and `shadow-lg` for popovers/modals. Avoid heavy dark shadows.
- **Transitions**: Every hoverable element MUST have `transition-all duration-200`.

## User Experience (UX) Patterns
- **Empty States**: Display a clean empty state with an icon (e.g., Heroicons) and a "Call to Action" if applicable.
- **Feedback**: 
  - Use Flash Messages / Toasts for CRUD success.
  - Form validation errors must be highlighted inline.
- **Modals**: Every "Delete" or "Danger" action must be confirmed via a modal.
- **Skeleton Screens**: Use skeleton loaders during AJAX/Livewire loading instead of full-page spinners.

## Layout & Spacing
- **Container**: Max width for dashboard content should usually be `max-w-7xl`.
- **Spacing**: Use a consistent 8pt grid (0.5rem increments). Standard padding for cards is `p-6`.
- **Responsive**: All views must be mobile-friendly. Use `grid-cols-1 md:grid-cols-2 lg:grid-cols-3` for listing cards.