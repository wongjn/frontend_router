Changelog
=========

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

[Unreleased]
------------

[1.1.0] - 2018-11-05
--------------------
### Changed
- Check ajax page state
- Expect valid HTML document

[1.0.9] - 2018-10-01
--------------------
### Changed
- Deny static page cache for fragment routes

[1.0.8] - 2018-09-21
--------------------
### Changed
- Move changeable regions data to `third_party_settings`

### Added
- Add schema

[1.0.7] - 2018-09-01
--------------------
### Changed
- Filter full response content for noscript tags
- Revert "Fix malformed content when disabled twig debug"

[1.0.6] - 2018-08-31
--------------------
### Fixed
- Fix malformed fragment response content when twig debug is not enabled

[1.0.5] - 2018-08-31
--------------------
### Changed
- Remove `<noscript>` tags from fragment responses

### Fixed
- Fix documentation
- Fix various code standards violations
