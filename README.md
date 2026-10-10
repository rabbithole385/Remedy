# Remedy Investment

A pixel-perfect replica of the Task Investment platform — built with pure HTML, Tailwind CSS (CDN), Alpine.js, and Lucide Icons.

## Pages

| Page | File | Description |
|------|------|-------------|
| Login | `login.html` | Sign-in form with animated background |
| Register | `register.html` | Registration form with password strength meter |
| Dashboard | `dashboard.html` | Full dashboard UI with sidebar, stats, chart & transactions |

## Tech Stack

- **Tailwind CSS** (CDN v3) — utility-first styling with custom brand tokens
- **Alpine.js** — reactive UI interactions (theme toggle, form states, sidebar)
- **Lucide Icons** — clean icon set
- **Chart.js** — earnings line chart on the dashboard
- **Inter** — Google Fonts typography

## Design System

| Token | Value |
|-------|-------|
| Brand color | `#0ea5e9` (sky-500) |
| Font | Inter (400, 500, 600, 700) |
| Dark BG | `zinc-950` |
| Card glass | `rgba(24,24,27,0.6)` + blur |
| Border | `rgba(255,255,255,0.07)` |

## Features

- 🌙 Dark / Light mode with `localStorage` persistence
- 📊 Interactive earnings chart
- 🔑 Password visibility toggle + strength meter (register)
- 🔔 Notification badge
- 📱 Responsive sidebar (collapsible)
- ✨ Animated floating background orbs
- 🔄 Loading spinner on form submit

## Usage

Open any `.html` file directly in a browser — no build step required.
