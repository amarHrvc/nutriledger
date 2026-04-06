# Drawers Catalog

Component variants for common drawer patterns across Vuexy. Use this to discover available drawer implementations and their dependencies.

---

## Available Drawers

| Name              | Path                                    | Description                                             | Bundle-Deps                        | Reusable |
|-------------------|-----------------------------------------|---------------------------------------------------------|------------------------------------|----------|
| TableFilters      | src/views/apps/user/list/               | Role/plan/status dropdown filters, controlled state, live filtering on change | No external deps (filter logic lives in parent) | yes      |
| AddUserDrawer     | src/views/apps/user/list/               | User creation form with React Hook Form, required field validation, optional fields (company, country, contact), auto-avatar generation | React Hook Form, react-hook-form, userTypes | yes      |

---

## Drawer Pattern

**Reusable:** Drawer is reusable when component takes state/callbacks as props and can be combined with different parent containers.

**Bundle-Deps:** Critical dependencies for each drawer variant:
- Filter drawers: Typically require no external data (pure UI), but parent must handle state + filtering logic
- Form drawers (Add/Edit): Require form library (React Hook Form) and TypeScript type definitions
- Both types should accept open/closed state and callbacks (onClose, onSubmit) as props

**State Management:**
- Filter drawers: Usually controlled by parent component (table, page component)
- Form drawers: May use React Hook Form internally or accept formState as prop
- Drawer visibility: Always controlled by parent (open/handleClose pattern)

---

## Next Steps

Once you've selected a drawer from this catalog:
1. Read `_knowledge/components/drawers/<drawer-name>.md` for implementation details
2. Review the `Bundle-Deps` to understand form libraries or utility requirements
3. Check `pages/apps/<domain>.md` for how the drawer is integrated into a full page
