# Contributing to Laravel HRM System

Thank you for considering contributing to the Laravel HRM System! This document provides guidelines and instructions for contributors.

## 🤝 How to Contribute

### Reporting Issues
- Use the [GitHub issue tracker](https://github.com/riad1302/HRM/issues)
- Check if the issue already exists before creating a new one
- Provide detailed information including:
  - Steps to reproduce the issue
  - Expected behavior
  - Actual behavior
  - Environment details (OS, PHP version, Laravel version)
  - Screenshots if applicable

### Suggesting Enhancements
- Use the issue tracker with the "enhancement" label
- Clearly describe the enhancement and its benefits
- Consider the scope and complexity of the feature

## 🔧 Development Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL
- Git

### Local Development
1. **Fork and Clone**
   ```bash
   git clone https://github.com/your-username/HRM.git
   cd HRM
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   # Configure database in .env
   ```

4. **Database Setup**
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Build Assets**
   ```bash
   npm run dev
   ```

6. **Start Development Server**
   ```bash
   php artisan serve
   ```

## 📝 Coding Standards

### PHP Standards
- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Add appropriate documentation/comments
- Follow Laravel conventions and best practices

### Code Structure
- **Controllers**: Keep them thin, delegate business logic to services
- **Services**: Contain business logic and data manipulation
- **Requests**: Handle validation rules
- **Models**: Define relationships and model-specific logic only

### Database
- Use proper migration files for schema changes
- Include rollback methods in migrations
- Use model factories for test data
- Follow Laravel naming conventions

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test files
php artisan test tests/Feature/EmployeeTest.php
php artisan test tests/Unit/EmployeeServiceTest.php
```

### Writing Tests
- Write tests for all new features
- Include both positive and negative test cases
- Use descriptive test method names
- Follow the existing test structure

### Test Categories
- **Feature Tests**: Test complete user workflows
- **Unit Tests**: Test individual service methods
- **Form Validation Tests**: Test validation rules
- **AJAX Tests**: Test AJAX endpoints

## 🔄 Pull Request Process

### Before Submitting
1. **Create a Feature Branch**
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make Your Changes**
   - Follow coding standards
   - Write/update tests
   - Update documentation if needed

3. **Test Your Changes**
   ```bash
   php artisan test
   ```

4. **Commit Changes**
   ```bash
   git add .
   git commit -m "Add: descriptive commit message"
   ```

### Commit Message Format
Use descriptive commit messages:
- `Add: new feature description`
- `Fix: bug description`
- `Update: modification description`
- `Remove: what was removed`
- `Refactor: refactoring description`

### Submitting the Pull Request
1. Push to your fork
   ```bash
   git push origin feature/your-feature-name
   ```

2. Create a pull request with:
   - Clear title and description
   - List of changes made
   - Screenshots for UI changes
   - Reference to related issues

### Pull Request Review
- All tests must pass
- Code review by maintainers
- Address any feedback promptly
- Keep the PR focused on a single feature/fix

## 📂 Project Structure

```
app/
├── Http/
│   ├── Controllers/     # Handle HTTP requests
│   └── Requests/        # Form validation
├── Models/             # Eloquent models
└── Services/           # Business logic

tests/
├── Feature/            # Integration tests
└── Unit/              # Unit tests

resources/
├── views/             # Blade templates
└── js/                # Frontend assets

database/
├── migrations/        # Database migrations
├── seeders/          # Database seeders
└── factories/        # Model factories
```

## 🎯 Areas for Contribution

### High Priority
- Bug fixes and security improvements
- Performance optimizations
- Test coverage improvements
- Documentation enhancements

### Medium Priority
- New features from the roadmap
- UI/UX improvements
- Code refactoring
- Additional validation rules

### Low Priority
- Minor feature additions
- Code style improvements
- Additional test cases

## 📋 Feature Requests

When proposing new features:

1. **Check the Roadmap**: Review existing plans in CHANGELOG.md
2. **Create an Issue**: Describe the feature in detail
3. **Discuss**: Engage with maintainers and community
4. **Plan**: Break down the feature into manageable tasks
5. **Implement**: Follow the development process

## 🐛 Bug Reports

When reporting bugs:

1. **Search First**: Check if the bug is already reported
2. **Reproduce**: Ensure the bug is reproducible
3. **Document**: Provide clear steps to reproduce
4. **Environment**: Include system and version information
5. **Evidence**: Add screenshots, logs, or error messages

## 📖 Documentation

### Areas Needing Documentation
- API endpoints documentation
- Service method documentation
- Complex business logic explanation
- Setup and deployment guides

### Documentation Standards
- Use clear, concise language
- Include code examples
- Add screenshots for UI features
- Keep documentation up-to-date

## 🔒 Security

### Reporting Security Issues
- **DO NOT** create public issues for security vulnerabilities
- Email security issues to: [your-email@example.com]
- Provide detailed information about the vulnerability
- Allow time for investigation and fix before disclosure

### Security Best Practices
- Validate all inputs
- Use Laravel's built-in security features
- Sanitize outputs
- Follow OWASP guidelines
- Keep dependencies updated

## 📞 Getting Help

### Resources
- [Laravel Documentation](https://laravel.com/docs)
- [PHP Documentation](https://php.net/docs.php)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)

### Community
- Create an issue for questions
- Join discussions in existing issues
- Follow the code of conduct

## 📜 Code of Conduct

### Our Standards
- Be respectful and inclusive
- Focus on constructive feedback
- Help others learn and grow
- Maintain professionalism

### Unacceptable Behavior
- Harassment or discrimination
- Trolling or personal attacks
- Publishing private information
- Any behavior deemed inappropriate

## 🏆 Recognition

Contributors will be:
- Listed in the project contributors
- Acknowledged in release notes
- Given credit for significant contributions

---

## 📧 Contact

- **Project Maintainer**: Habibur Rahman Riad
- **GitHub**: [@riad1302](https://github.com/riad1302)
- **Project Repository**: https://github.com/riad1302/HRM

Thank you for contributing to the Laravel HRM System! 🎉