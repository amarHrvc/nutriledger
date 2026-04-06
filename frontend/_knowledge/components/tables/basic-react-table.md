# BasicDataTables Component (Demo)

**File:** `src/views/react-table/BasicDataTables.tsx`  
**Reusable:** demo (learning/reference example)  
**Type:** Reference Implementation  

---

## Purpose

Minimal TanStack React Table v8 example demonstrating:
- Basic column definitions with columnHelper
- Simple data rendering (no filters, sorting, or pagination)
- Static dataset (10 rows)
- Table component structure with semantic HTML

---

## Usage

**Learning aid** - shows simplest possible table setup. Use as starting point before adding features like filtering, sorting, or pagination.

---

## Features

- **Columns:** 6 columns (ID, Name, Email, Date, Experience, Age)
- **Rows:** Static 10 rows from `data.ts`
- **No filtering, sorting, or pagination**
- **Minimal dependencies:** Only `@tanstack/react-table` + MUI Card

---

## Props

None - this is a standalone demo component.

---

## Bundle

- `@tanstack/react-table` ^8.x
- `@mui/material` ^5.x (Card, CardHeader)
- Static data import from `./data`

---

## Notes

- **Demo only** - not production-ready
- Use as template for understanding TanStack basics
- Real applications should add filtering, sorting, pagination
- See ProductListTable, UserListTable for full-featured examples
