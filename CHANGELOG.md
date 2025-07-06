# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2025-07-06

### Added
- Plugin filtering by status (`filter=active|inactive|all`)
- Health check functionality (`health=true`)
- Health summary statistics
- Enhanced response data with more detailed statistics
- Admin panel notification about API endpoint
- Comprehensive error handling
- File size reporting in health checks

### Enhanced
- Improved response format with additional metadata
- Better documentation and examples
- More robust plugin metadata extraction

### Fixed
- Array re-indexing after filtering to ensure clean JSON output

## [1.0.0] - 2025-07-06

### Added
- Initial release
- Basic plugin status API endpoint
- Complete plugin metadata extraction
- Support for JSON, XML, and simple formats
- Proper YOURLS API integration
- Security checks and authentication
