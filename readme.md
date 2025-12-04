# WP-CLI Make Command

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-35%20passing-brightgreen.svg)](tests/)
[![Coverage](https://img.shields.io/badge/coverage-85%25-yellow.svg)](coverage/)

A powerful WordPress WP-CLI command for creating posts with validation, duplicate detection, and multiple content sources.

## 🚀 Features

- **✅ Smart Validation** - Title, author, status validation
- **🔍 Duplicate Detection** - Prevent duplicate posts by title/slug
- **📁 Multiple Content Sources** - Direct, file, URL, STDIN, or auto-generated
- **🎯 Flexible Options** - Category, tags, excerpt, custom slug, meta data
- **🛡️ Error Handling** - Clear error messages with context
- **🧪 Tested** - 35+ unit tests with 85%+ coverage
- **🎨 Clean Architecture** - Factory pattern, DTOs, separation of concerns

## 📦 Installation

### Via Composer (Recommended)

```bash
# Install in your WordPress project
composer require vigihdev/wp-cli-make-command

# Or install globally
composer global require vigihdev/wp-cli-make-command
```

### Manual Installation

1. Clone this repository into your WordPress `wp-content/plugins` or `wp-content/mu-plugins` directory:

```bash
cd wp-content/plugins
git clone https://github.com/yourusername/wp-cli-make-command.git
```

2. Load the command in your `wp-config.php` or a plugin:

```php
// In wp-config.php (before WP loads)
if (defined('WP_CLI') && WP_CLI) {
    require_once __DIR__ . '/wp-content/plugins/wp-cli-make-command/src/commands.php';
}
```

## 🎯 Quick Start

```bash
# Create a basic post
wp make:post "Hello World" --content="Welcome to my site" --status=publish

# Create a draft post with category
wp make:post "New Article" --category=1 --status=draft

# Create from file content
wp make:post "Read from File" --content-file=article.txt

# Create with custom slug and excerpt
wp make:post "About Us" --slug="about" --excerpt="Learn about our company" --status=publish
```

## 📖 Usage

### Basic Syntax

```bash
wp make:post <title> [--<option>=<value>]
```

### Required Arguments

| Argument  | Description           | Example         |
| --------- | --------------------- | --------------- |
| `<title>` | Post title (required) | `"My New Post"` |

### Options

| Option               | Description                      | Default        | Example                                     |
| -------------------- | -------------------------------- | -------------- | ------------------------------------------- |
| `--content`          | Post content (use `-` for STDIN) | `""`           | `--content="Lorem ipsum"`                   |
| `--content-file`     | Read content from file           |                | `--content-file=article.md`                 |
| `--content-url`      | Fetch content from URL           |                | `--content-url=https://example.com/content` |
| `--generate-content` | Auto-generate content from title | `false`        | `--generate-content`                        |
| `--status`           | Post status                      | `draft`        | `--status=publish`                          |
| `--type`             | Post type                        | `post`         | `--type=page`                               |
| `--author`           | Author ID                        | `1`            | `--author=2`                                |
| `--category`         | Category ID or name              |                | `--category="Uncategorized"`                |
| `--tags`             | Comma-separated tags             |                | `--tags="tech,wordpress,php"`               |
| `--excerpt`          | Post excerpt                     |                | `--excerpt="Short summary"`                 |
| `--slug`             | Custom post slug                 | auto-generated | `--slug="my-custom-slug"`                   |
| `--date`             | Post date (Y-m-d H:i:s)          | current time   | `--date="2024-12-01 10:00:00"`              |
| `--parent`           | Parent post ID                   | `0`            | `--parent=123`                              |
| `--unique_title`     | Auto-modify if duplicate         | `false`        | `--unique_title`                            |

### Examples

#### Create a Published Post

```bash
wp make:post "Welcome Post" \
  --content="Welcome to our website!" \
  --status=publish \
  --category=1 \
  --tags="welcome,general"
```

#### Create Post from File

```bash
# From local file
wp make:post "Tutorial" --content-file=tutorial.md --status=publish

# From STDIN (piping)
cat article.txt | wp make:post "Article" --content=-
```

#### Create with Meta Data

```bash
wp make:post "Product" \
  --content="Product description" \
  --meta='{"_price": 1000, "_sku": "PROD001"}' \
  --status=publish
```

#### Bulk Create from CSV

```bash
# Create multiple posts (requires custom script)
while IFS=, read -r title content; do
  wp make:post "$title" --content="$content" --status=draft
done < posts.csv
```

## 🏗️ Architecture

The package follows clean architecture principles:

```
src/
├── Commands/          # WP-CLI command handlers
├── Factory/           # Post creation logic
│   ├── Contracts/     # Interfaces
│   ├── DTOs/         # Data Transfer Objects
│   └── PostFactory.php
├── Utils/            # Utilities (ContentResolver, HttpClient)
├── Exceptions/       # Custom exceptions
└── commands.php      # Command registration
```

### Key Components

1. **PostFactory** - Handles post creation with validation
2. **ContentResolver** - Resolves content from multiple sources
3. **DTOs** - Type-safe data transfer objects
4. **Custom Exceptions** - Detailed error handling

## 🧪 Testing

```bash
# Run all tests
composer test

# Run with coverage report
composer test:coverage

# Open coverage report
open coverage/index.html
```

### Test Structure

```
tests/
├── Unit/             # Unit tests (pure PHP)
├── Integration/      # Integration tests (with WP mocks)
├── Feature/          # Feature tests (command usage)
└── bootstrap.php     # Test environment setup
```

## 🔧 Development

### Prerequisites

- PHP 8.1+
- Composer
- WordPress (for integration testing)
- WP-CLI

### Setup Development Environment

```bash
# Clone repository
git clone https://github.com/yourusername/wp-cli-make-command.git
cd wp-cli-make-command

# Install dependencies
composer install

# Run tests
composer test

# Run code quality checks
composer check
```

### Adding New Features

1. Create feature branch: `git checkout -b feature/new-feature`
2. Write tests first (TDD approach)
3. Implement the feature
4. Ensure all tests pass
5. Update documentation
6. Submit pull request

## 📚 API Reference

### Using PostFactory Programmatically

```php
use Vigihdev\WpCliMake\Factory\PostFactory;

// Create a post
$result = PostFactory::create('My Post', [
    'content' => 'Post content',
    'status' => 'publish',
    'category' => 1,
]);

if ($result->isCreated()) {
    echo "Post created with ID: " . $result->getPost()->ID;
} elseif ($result->isDuplicate()) {
    echo "Duplicate post detected";
} else {
    echo "Error: " . $result->getError();
}
```

### Custom Exceptions

```php
use Vigihdev\WpCliMake\Exceptions\{
    ContentFetchException,
    ValidationException,
    PostCreationException
};

try {
    // Your code here
} catch (ContentFetchException $e) {
    echo "Content error: " . $e->getMessage();
} catch (ValidationException $e) {
    echo "Validation errors: " . implode(', ', $e->getErrors());
}
```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Code Style

- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation
- Use type hints and strict types

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- [WP-CLI](https://wp-cli.org/) team for the amazing CLI framework
- WordPress community for inspiration
- All contributors who help improve this package

## 📞 Support

- **Issues**: [GitHub Issues](https://github.com/yourusername/wp-cli-make-command/issues)
- **Email**: vigihdev@gmail.com

## 🚀 Roadmap

- [ ] Add bulk creation from JSON/CSV
- [ ] Support custom post types
- [ ] Add featured image from URL
- [ ] Import from Markdown with frontmatter
- [ ] Scheduled posts with cron
- [ ] Integration with popular page builders

<div align="center">
Made with ❤️ by <a href="https://github.com/yourusername">Vigihdev</a>
<br>
If this project helps you, please give it a ⭐️
</div>
