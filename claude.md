# Claude MCP Servers Configuration

## Playwright MCP Server

**Repository**: [microsoft/playwright-mcp](https://github.com/microsoft/playwright-mcp)

### Description
The Playwright MCP server enables Claude to interact with web browsers through the Model Context Protocol (MCP). It provides browser automation capabilities including navigation, interaction with page elements, form filling, screenshot capture, and more.

### Features
- Browser automation and web page interaction
- Screenshot and PDF generation
- Form filling and element interaction
- Page navigation and waiting for elements
- Console log capture
- Network request monitoring

### Installation

```bash
npm install -g playwright-mcp
```

### Configuration

Add to your Claude Desktop configuration file:

**MacOS**: `~/Library/Application Support/Claude/claude_desktop_config.json`
**Windows**: `%APPDATA%/Claude/claude_desktop_config.json`

```json
{
  "mcpServers": {
    "playwright": {
      "command": "playwright-mcp",
      "args": []
    }
  }
}
```

### Usage
Once configured, Claude can use Playwright to:
- Navigate to websites
- Take screenshots
- Fill out forms
- Click buttons and links
- Extract page content
- Monitor network traffic
- Execute JavaScript on pages

### Use Cases
- Automated testing
- Web scraping
- Form automation
- Visual regression testing
- Browser-based workflows
- End-to-end testing assistance

---

For more information, visit: https://github.com/microsoft/playwright-mcp
