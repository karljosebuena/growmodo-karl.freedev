# 07 — Accessibility Coding Standards

Source: <https://developer.wordpress.org/coding-standards/wordpress-coding-standards/accessibility/>

---

## Required level

All WordPress code must conform to **WCAG 2.2 at Level AA**. Level AAA conformance is
encouraged where relevant. New interfaces should also incorporate **ATAG 2.0** principles
by *encouraging* authors to add alternative text, captions and semantic structure.

---

## The four principles

| Principle | Meaning |
| --- | --- |
| **Perceivable** | Content is available through multiple formats — text alternatives for images, captions for video |
| **Operable** | All functionality works via keyboard and assistive technologies |
| **Understandable** | Clear language, meaningful labels, abbreviations explained |
| **Robust** | Content works with current *and future* assistive technologies |

---

## Key guideline areas

1. **Text alternatives (1.1)** — provide text alternatives for any non-text content.
2. **Time-based media (1.2)** — captions and transcripts for audio and video.
3. **Keyboard accessibility (2.1)** — all functionality available from the keyboard.
4. **Navigation (2.4)** — help users find content and know where they are.
5. **Readable content (3.1)** — make text understandable.
6. **Input assistance (3.3)** — help users avoid and correct mistakes.
7. **Compatible (4.1)** — maximise compatibility with assistive technologies.

---

## Practical implementation checklist

### Semantics and structure

- One `<h1>` per page; heading levels descend without skipping.
- Landmarks: `<header>`, `<nav>`, `<main>`, `<aside>`, `<footer>`.
- Lists for lists, `<table>` with `<th scope>` for tabular data — never for layout.
- Native elements before ARIA: a `<button>` beats `<div role="button">`.

### Forms

- Every control has a programmatic label: `<label for>`, or `aria-label` / `aria-labelledby`.
- Placeholder text is **not** a label.
- Group related controls with `<fieldset>` + `<legend>`.
- Required and invalid states exposed: `required`, `aria-invalid`, `aria-describedby`
  pointing at the error message.
- Errors identified in text, not by colour alone; describe how to fix them.

### Keyboard and focus

- Everything interactive is reachable and operable by keyboard.
- Logical tab order; don't use positive `tabindex`.
- **Never remove focus outlines** — restyle them instead. WCAG 2.2 requires that focus is
  not obscured and has adequate appearance.
- Modals trap focus while open, return focus to the trigger on close, and close on `Esc`.
- Provide a skip link to the main content.
- Target size (WCAG 2.2, 2.5.8): at least 24×24 CSS px.

### Images and media

- `alt` describes purpose; decorative images use `alt=""`.
- Complex images (charts) get a longer text description.
- Video: captions; audio: transcripts. No autoplay with sound.

### Links and buttons

- Link text makes sense out of context — no bare "click here" / "read more"; add
  `<span class="screen-reader-text">` context if the visible text must stay short.
- A link navigates, a button acts. Use the right element.
- Warn when a link opens in a new window/tab.

### Colour and text

- Contrast: **4.5:1** for normal text, **3:1** for large text and UI components/graphics.
- Never convey meaning with colour alone.
- Text must reflow and remain usable at 200% zoom and 400% (320 px) viewport width.
- Respect `prefers-reduced-motion`.

### WordPress-specific helpers

```php
// Visually hidden but announced by screen readers.
echo '<span class="screen-reader-text">' . esc_html__( 'Search for:', 'growmodo' ) . '</span>';

// Skip link target.
<a class="skip-link screen-reader-text" href="#content">
	<?php esc_html_e( 'Skip to content', 'growmodo' ); ?>
</a>
```

- Use core's `.screen-reader-text` / `.screen-reader-text.skip-link` CSS conventions.
- Announce dynamic changes with `wp.a11y.speak()` (the `wp-a11y` script dependency) or an
  `aria-live` region.
- Register nav menus so core outputs semantic markup; keep `aria-current` on the active item.

---

## Testing guidance

Success criteria "can be carried out using automated software and/or human testers", and
**usability testing is still important and should be carried out alongside any
accessibility testing** — automation cannot judge whether an experience makes sense.

Suggested pass, in order:

1. **Automated:** axe DevTools / Lighthouse / `pa11y`, plus the W3C HTML validator.
2. **Keyboard only:** unplug the mouse and complete every flow with Tab / Shift+Tab /
   Enter / Space / arrows / Esc.
3. **Screen reader:** VoiceOver (macOS/iOS), NVDA (Windows), TalkBack (Android).
4. **Zoom & reflow:** 200% zoom, 320 px width.
5. **Contrast:** check every text/background and UI-state combination.
6. **Human review:** does the announced order and wording actually make sense?
