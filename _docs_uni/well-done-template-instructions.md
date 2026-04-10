# Instructions: What a Well-Done Senior Design Project Template Looks Like

Based on analysis of the official IBU template and the Fitness Tracker example project.

---

## Front Matter

### Cover Page
- University, faculty, department
- Full project title (all caps)
- Student full name
- Mentor name with academic title
- City and month/year

### Approval Page
- Filled table: student name, faculty, department, project title, date of defense
- Head of Department signature line (Assoc. Prof. Dr. Dino Kečo)
- Mentor signature line with name and title
- Examining Committee table: 3 members with Title/Name, Affiliation, Signature columns

### Abstract
- Must follow the 3-part structure: **problem statement → methods/procedures → results and conclusion**
- One coherent paragraph, **max 350 words**
- No references, diagrams, or footnotes
- End with **Keywords:** (3–5 relevant terms)
- Example of good abstract opening: *"The objective of this project is to develop [X] that is designed to [purpose]. The problem addressed is [specific gap]..."*

### Acknowledgments
- Thank mentor by name and title
- Thank colleagues, staff, family
- Keep personal and sincere

### Declaration
- Standard declaration text with **bold project title**
- Student signature line + date

### Table of Contents
- Must include **all sections and subsections** that appear in the document
- Use numbered heading format: `2.1.`, `2.1.1.`, etc.
- Bold chapter-level headings

### List of Tables / Table of Figures
- Each entry: `Figure X.Y. Caption text ... page_number`
- Numbering format: chapter number dot figure number (e.g., Figure 3.4.1.)

### List of Abbreviations
- All abbreviations used in the document, bolded, aligned

---

## Chapter 1 — Introduction

- State the **problem** being solved
- Explain **why** the problem matters (context, current gaps)
- State the **aim/objective** of the project
- Brief mention of the proposed solution
- 1–2 paragraphs minimum; should not describe implementation details

---

## Chapter 2 — System Analysis

### 2.1. System Overview
Required subsections:
- **2.1.1. Product Perspective** — what the system does at a high level
- **2.1.2. Target Audience** — list each user group with a bullet + explanation of their benefit
- **2.1.3. Project Constraints and Risks** — at least one concrete risk with explanation
- **2.1.4. Success Criteria** — measurable indicators of success

### 2.2. Requirements Analysis

#### 2.2.1. Functional Requirements
Each requirement must follow this format:

```
**N. Requirement Name**
As a [role], I want to [action] so I can [benefit].

**Acceptance Criteria:**
1. Step-by-step description of the flow
2. Include both happy path and error/invalid input cases
3. Typically 5–10 numbered steps per requirement
```

- Role options: `user`, `registered user`, `logged in user`, `admin`
- Number sequentially — **do not reuse numbers**
- Cover all major features of the application (typically 10–20 requirements)
- Include both user-facing and admin requirements

#### 2.2.2. Non-Functional Requirements
Group by category, each as a bold heading with 1–2 bullet points:
- **Security** — authentication, authorization, data protection
- **Usability** — UI intuitiveness, learnability
- **Scalability** — user load targets, infrastructure
- **Compliance** — relevant regulations
- Add others as relevant (Performance, Reliability, etc.)

---

## Chapter 3 — Application Design

Introduce the chapter briefly (1 sentence is enough).

### 3.1. Use Case Diagram
- List the primary actors before showing the diagram
- Figure caption below the diagram: *Figure 3.1. Use Case Diagram*

### 3.2. Activity Diagrams
- One activity diagram **per major user flow/functional requirement**
- Before each diagram, write 1–2 sentences describing what the diagram illustrates:
  > *"Figure 3.2.X. illustrates the steps of [action], from [start] to [end]."*
- Match diagram count to the number of key flows (aim for coverage of all major features)

### 3.3. Class Diagram
- 1–2 sentences explaining what the diagram shows and what entities are included
- Show all major model classes with attributes, methods, and associations

### 3.4. Sequence Diagrams
- One sequence diagram **per major user flow** (should mirror the activity diagrams)
- Before each diagram, write 1–2 sentences describing which components interact and what is shown:
  > *"Figure 3.4.X. illustrates the interactions between [actor] and [system components]. It shows [key process]."*

**Numbering rule:** Figure numbers must match the section. `Figure 3.2.1.` is in section 3.2, `Figure 3.4.5.` is in section 3.4. Do not mislabel (e.g., do not write `3.3.3.` when you mean `3.2.3.`).

---

## Chapter 4 — Implementation

### 4.1. Implementation Overview
- 1 paragraph summarizing the technology stack and architecture decisions

### 4.2. Backend Technologies
- For each major technology: explain what it is, why it was chosen, and cite a reference in `[N]` format
- Example: *"Spring Boot... It is used to create stand-alone Spring-based applications... [1]."*
- Cover: framework, database, authentication, any real-time/messaging tech

### 4.3. Frontend Technologies
- Same pattern: technology name, description, purpose, citation
- Cover: UI framework, language, state management, routing, component library

### 4.4. Integrations and Services
- External APIs and services used (cloud storage, email, payments, etc.)
- Explain why each was chosen and what it handles

### 4.5. Results
- Screenshots of the working application
- Before each screenshot: 1 sentence describing what the figure shows
- Cover all major pages/features
- Figures: `Figure 4.5.N. Page Name`

---

## Chapter 5 — System Testing

- Introductory paragraph: what testing framework was used and why, mention of mocking framework if applicable
- Include a screenshot of the test run (all passing)

### 5.1. Description of Unit Tests
Organize by test class. For each class, briefly state what it tests. For each test method:

```
N. MethodName()

**Purpose:** What the test verifies.
**Setup:** How the test object/mock is prepared.
**Verification:** What assertions are made and how.
```

- Cover all test classes: model, repository, service, controller
- Explain Mockito mocking where used

---

## Chapter 6 — Maintenance Analysis

Address **both current state and future plans**:

**Current:**
- How the deployed infrastructure ensures availability (e.g., managed cloud DB)
- Security mechanisms in place
- Data storage strategy

**Future:**
- At least 2–3 concrete future improvements (e.g., microservices, refresh tokens, caching)
- Explain the motivation for each improvement

---

## Chapter 7 — Conclusion

### 7.1. Future Work
- List potential enhancements and new features
- Explain why each would improve the system

### 7.2. Final Words (or Summary)
- Personal reflection on challenges, what was learned
- Brief restatement of what was achieved

---

## References

- Use numbered format: `[1]`, `[2]`, etc.
- Cite inline in the text as `[1]` wherever a technology or fact is referenced
- Format: `[N] Author/Organization (year). *Title*. Retrieved [date] from [URL]`
- Every in-text citation must have a corresponding reference entry

---

## Common Mistakes to Avoid

| Mistake | Fix |
|---|---|
| Duplicate requirement numbers | Number all functional requirements sequentially |
| Cross-reference errors (e.g., Figure 3.3.3 instead of 3.2.3) | Double-check figure numbers against their section |
| Missing diagrams for some features | Ensure activity + sequence diagram coverage matches requirements |
| Vague acceptance criteria | Each step should describe a specific UI action or system response |
| Technologies described without citations | Every external technology needs a `[N]` citation |
| Table of Contents missing subsections | Include all headings down to level 3 |
| Abstract exceeds 350 words | Trim; keep only problem/methods/results/conclusion |
| Missing error/invalid-input cases in acceptance criteria | Every flow with user input needs a validation failure path |
