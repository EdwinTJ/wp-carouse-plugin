# AI Coding Standards & Agent Rules

## General Context
- Project Name: Performance Carousel
- Core Principle: Performance-first modular development.
- Version Source: Always check the header in performance-carousel.php. (Current Version: 0.1)

## PHP Documentation Standards (.php)

### Function Blocks
All functions must include a PHPDoc block using the following structure:
```php
/**
 * Does something interesting
 *
 * @param Place   $where  Where something interesting takes place
 * @param integer $repeat How many times something interesting should happen
 * * @throws Some_Exception_Class If something interesting cannot happen
 * @author Monkey Coder <mcoder@facebook.com>
 * @return Status
 */
```
### Class Blocks
All classes must include a summary and a `@since` tag:
```php
/**
 * Short description for class
 *
 * Long description for class (if any)...
 *
 * @since      Class available since Release 1.2.0
 */
```

## JavaScript & TypeScript Standards

### JavaScript (.js)
Include types within curly braces in the JSDoc:
```js
/**
 * Does something nifty.
 *
 * @param   {number} whatsit  The whatsit to use (or whatever).
 * @returns {string} A useful value.
 */
function nifty(whatsit) {
    return /*...*/;
}
```

### TypeScript (.ts)
Note: Use JSDoc braces `{}` for types even though they are defined in the function signature.
```ts
/**
 * Does something nifty.
 *
 * @param   {number} whatsit  The whatsit to use (or whatever).
 * @returns {string} A useful value.
 */
function nifty(whatsit: number): string {
    return /*...*/;
}
```