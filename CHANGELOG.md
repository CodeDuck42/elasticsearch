# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased](https://github.com/CodeDuck42/elasticsearch/compare/v0.5...HEAD)

### Changed
- Moved functional testing to the regular test classes

## [v0.5](https://github.com/CodeDuck42/elasticsearch/compare/v0.4...v0.5)

### Added

- Added a simpler client that works on a fixed index
- Created interfaces for the clients for easier mocking
- Added a documentation file for the exception structure

### Changed
- Moved value objects into a sub namespace
- Moved interfaces into the Contracts namespace
- Reorganized the namespace structure
- Shortened the names of the most specific exceptions

## [v0.4](https://github.com/CodeDuck42/elasticsearch/compare/v0.3.1...v0.4)

### Added
- Added the changelog file
- Added todo for the missing documentation
- Added mutator testing via infection

### Changed
- Switched all badges in the readme to shields.io
- Modified the example code in the readme

### Removed
- Removed last commit badge from the readme

## [v0.3.1](https://github.com/CodeDuck42/elasticsearch/compare/v0.3...v0.3.1)

### Added
- Added coverage reports via codecov

### Changed
- Refactored the github workflows
- Modified the readme to reflect the changes

## [v0.3](https://github.com/CodeDuck42/elasticsearch/compare/0.2.1...v0.3)

### Added
- Added a new bulk action
- Added missing unit tests
- Added integration tests to check against elasticsearch 6.x and 7.x

### Changed
- Renamed lots of methods

## [v0.2.1](https://github.com/CodeDuck42/elasticsearch/compare/0.2...0.2.1)

### Changed
- Changed some badges in the readme

### Fixed
- Fixed the example code in the documentation

## [v0.2](https://github.com/CodeDuck42/elasticsearch/compare/v0.1...0.2)

### Changed
- Added more objects to reduce the usage of plain old arrays

## v0.1

### Added
- First usable MVP checked in
