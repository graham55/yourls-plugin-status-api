# YOURLS Plugin Status API

A comprehensive YOURLS plugin that adds a custom API endpoint to query plugin status, metadata, and health information.

Directory Structure :
```text
plugin-status-api/
├── plugin.php
├── README.md
├── CHANGELOG.md
├── LICENSE
└── .gitignore
```

![YOURLS Plugin](https://img.shields.io/badge/YOURLS-Plugin-blue)
![Version](https://img.shields.io/badge/version-1.1.0-green)
![License](https://img.shields.io/badge/license-MIT-blue)

## Features

- 📊 **Complete Plugin Inventory**: List all installed plugins with full metadata
- 🔍 **Flexible Filtering**: Filter by active, inactive, or all plugins
- 🏥 **Health Monitoring**: Optional health checks for plugin files
- 📈 **Statistics**: Comprehensive plugin statistics and summaries
- 🔒 **Secure**: Uses YOURLS authentication system
- 📱 **Multiple Formats**: Supports JSON, XML, and simple text formats

## Installation

1. Download the latest release or clone this repository
2. Extract/upload the `plugin-status-api` folder to your YOURLS `/user/plugins/` directory
3. Go to the YOURLS admin panel → Plugins page
4. Activate the "Plugin Status API" plugin

## Usage

### Basic Usage

GET /yourls-api.php?signature=YOUR_SIGNATURE&action=plugin_status&format=json


### Parameters

| Parameter | Values | Description |
|-----------|--------|-------------|
| `signature` | string | **Required.** Your YOURLS API signature |
| `action` | `plugin_status` | **Required.** Must be `plugin_status` |
| `format` | `json`, `xml`, `simple` | Response format (default: `json`) |
| `filter` | `all`, `active`, `inactive` | Filter plugins by status (default: `all`) |
| `health` | `true`, `false` | Include health check data (default: `false`) |

### Examples

**Get all plugins:**
/yourls-api.php?signature=xxx&action=plugin_status&format=json


**Get only active plugins:**
/yourls-api.php?signature=xxx&action=plugin_status&filter=active&format=json


**Get plugins with health checks:**
/yourls-api.php?signature=xxx&action=plugin_status&health=true&format=json


## Response Format

### Basic Response

{
    "statusCode": 200,
    "simple": "8 plugins found, 3 total active",
    "message": "success",
    "plugin_status": {
        "total_plugins": 8,
        "active_plugins": 3,
        "inactive_plugins": 5,
        "filtered_count": 8,
        "filter_applied": "all",
        "plugins": [
            {
                "plugin_file": "sample-plugin/plugin.php",
                "name": "Sample Plugin",
                "description": "A sample plugin for demonstration",
                "version": "1.0",
                "author": "Author Name",
                "plugin_uri": "https://github.com/author/plugin",
                "author_uri": "https://author-website.com",
                "active": true
            }
        ]
    }
}


### With Health Checks

When `health=true` is included, each plugin gets additional health data:

{
    "plugin_file": "sample-plugin/plugin.php",
    "name": "Sample Plugin",
    "active": true,
    "health": {
        "file_exists": true,
        "readable": true,
        "has_header": true,
        "file_size": 2048
    }
}


Plus a health summary:

"health_summary": {
    "healthy_plugins": 7,
    "total_checked": 8,
    "health_percentage": 87.5,
    "total_size_bytes": 45632,
    "issues": [
        {
            "plugin": "Broken Plugin",
            "issues": [
                "file_missing",
                "invalid_header"
            ]
        }
    ]
}



## Response Fields

### Plugin Object

| Field | Type | Description |
|-------|------|-------------|
| `plugin_file` | string | Relative path to plugin file |
| `name` | string | Plugin name from header |
| `description` | string | Plugin description |
| `version` | string | Plugin version |
| `author` | string | Plugin author |
| `plugin_uri` | string | Plugin homepage URL |
| `author_uri` | string | Author website URL |
| `active` | boolean | Whether plugin is currently active |
| `health` | object | Health check data (if requested) |

### Health Object

| Field | Type | Description |
|-------|------|-------------|
| `file_exists` | boolean | Whether plugin file exists |
| `readable` | boolean | Whether plugin file is readable |
| `has_header` | boolean | Whether plugin has valid header |
| `file_size` | integer | Plugin file size in bytes |

## Requirements

- YOURLS 1.7.3 or higher
- PHP 7.0 or higher
- Valid YOURLS API signature for authentication

## Error Handling

The plugin returns standard YOURLS API error responses:

{
"statusCode": 400,
"message": "Invalid filter parameter"
}

text

## Security

- Uses YOURLS built-in authentication system
- No additional permissions required beyond API access
- Respects YOURLS security settings

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a detailed history of changes.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

- 🐛 **Bug Reports**: [GitHub Issues](https://github.com/graham55/yourls-plugin-status-api/issues)
- 💡 **Feature Requests**: [GitHub Issues](https://github.com/graham55/yourls-plugin-status-api/issues)
- 📖 **Documentation**: This README and inline code comments

## Author

**Graham McKoen (GPM)**
- GitHub: [@graham55](https://github.com/graham55)

---

Made with ❤️ for the YOURLS community
