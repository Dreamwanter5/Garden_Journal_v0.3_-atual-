// Simple Markdown parser
function parseMarkdown(markdown) {
return (
    markdown
    // Headers
    .replace(/^###### (.*$)/gim, "<h6>$1</h6>")
    .replace(/^##### (.*$)/gim, "<h5>$1</h5>")
    .replace(/^#### (.*$)/gim, "<h4>$1</h4>")
    .replace(/^### (.*$)/gim, "<h3>$1</h3>")
    .replace(/^## (.*$)/gim, "<h2>$1</h2>")
    .replace(/^# (.*$)/gim, "<h1>$1</h1>")
    // Bold and Italic
    .replace(/\*\*(.*)\*\*/gim, "<strong>$1</strong>")
    .replace(/\*(.*)\*/gim, "<em>$1</em>")
    .replace(/~~(.*)~~/gim, "<del>$1</del>")
    // Code
    .replace(/`(.*?)`/gim, "<code>$1</code>")
    .replace(/```([\s\S]*?)```/gim, "<pre><code>$1</code></pre>")
    // Blockquotes
    .replace(/^> (.*$)/gim, "<blockquote>$1</blockquote>")
    // Horizontal Rule
    .replace(/^\-\-\-$/gim, "<hr>")
    // Lists
    .replace(/^\s*[\-\*\+] (.*$)/gim, "<ul><li>$1</li></ul>")
    .replace(/^\s*\d+\. (.*$)/gim, "<ol><li>$1</li></ol>")
    // Fix nested lists
    .replace(/<\/ul>\s*<ul>/gim, "")
    .replace(/<\/ol>\s*<ol>/gim, "")
    // Links
    .replace(
        /\[([^\[]+)\]\(([^\)]+)\)/gim,
        '<a href="$2" target="_blank">$1</a>'
    )
    // Images
    .replace(/!\[([^\[]+)\]\(([^\)]+)\)/gim, '<img src="$2" alt="$1">')
    // Paragraphs
    .replace(/^\s*(\n)?(.+)/gim, function (m) {
        return /\<(\/)?(h\d|ul|ol|li|blockquote|pre|img)/.test(m)
        ? m
        : "<p>" + m + "</p>";
    })
    // Line breaks
    .replace(/\n$/gim, "<br>")
);
}

// DOM elements
const editor = document.getElementById("editor");
const preview = document.getElementById("preview");
const clearBtn = document.getElementById("clear-btn");
const sampleBtn = document.getElementById("sample-btn");
const downloadBtn = document.getElementById("download-btn");

// Sample content
const sampleContent = `# Bem-vindo ao Editor Markdown!

Este é um exemplo de **editor Markdown** com visualização em tempo real.

## Funcionalidades

- ✨ **Visualização em tempo real**
- 🎨 **Design moderno e responsivo**
- 📱 **Compatível com dispositivos móveis**
- 🚀 **Rápido e leve**

## Exemplos de Sintaxe

### Texto Formatado

**Negrito**, *itálico*, ~~tachado~~, e \`código inline\`.

### Listas

1. Item ordenado 1
2. Item ordenado 2
3. Item ordenado 3

- Item não ordenado
- Outro item
- Subitem

### Código

\`\`\`javascript
function exemplo() {
console.log("Olá, Mundo!");
return true;
}
\`\`\`

### Citações

> Esta é uma citação elegante que destaca informações importantes.

### Links e Imagens

Visite o [Markdown Guide](https://www.markdownguide.org) para aprender mais.

---

*Divirta-se escrevendo!*`;

// Update preview
function updatePreview() {
preview.innerHTML = parseMarkdown(editor.value);
}

// Event listeners
editor.addEventListener("input", updatePreview);

clearBtn.addEventListener("click", () => {
editor.value = "";
updatePreview();
});

sampleBtn.addEventListener("click", () => {
editor.value = sampleContent;
updatePreview();
});

downloadBtn.addEventListener("click", () => {
const htmlContent = `
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Documento Markdown</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    color: #333;
}
h1, h2, h3 { color: #2d3748; }
code { background: #f7fafc; padding: 2px 6px; border-radius: 4px; }
pre { background: #2d3748; color: #e2e8f0; padding: 1em; border-radius: 8px; overflow-x: auto; }
blockquote { border-left: 4px solid #4c6ef5; padding-left: 1em; margin-left: 0; color: #4a5568; font-style: italic; }
</style>
</head>
<body>
${preview.innerHTML}
</body>
</html>`;

const blob = new Blob([htmlContent], { type: "text/html" });
const url = URL.createObjectURL(blob);
const a = document.createElement("a");
a.href = url;
a.download = "documento-markdown.html";
document.body.appendChild(a);
a.click();
document.body.removeChild(a);
URL.revokeObjectURL(url);
});

// Initialize preview
updatePreview();