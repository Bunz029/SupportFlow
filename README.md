# SupportFlow - Support Ticket Management System

SupportFlow is a comprehensive support ticket management system built with Laravel, designed to streamline communication between clients and support teams. The system provides an efficient way to manage support requests, track ticket progress, and maintain a knowledge base.

## Features

### For Clients
- Easy ticket submission and tracking
- Real-time status updates
- File attachment support
- Access to knowledge base articles
- Direct communication with support team
- Email notifications for ticket updates
- Company and user profile management

### For Support Agents
- Ticket management dashboard
- Real-time ticket assignment
- Response management
- Knowledge base article creation
- Performance tracking
- Client communication history

### For Administrators
- Complete system oversight
- User management (clients, agents)
- SLA monitoring and reporting
- System settings configuration
- Performance analytics
- Knowledge base management
- Notification settings

## Technical Requirements

- PHP 8.1 or higher
- Laravel 10.x
- MySQL 5.7 or higher
- Composer
- Node.js and NPM
- Web server (Apache/Nginx)

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd supportflow
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install and compile frontend assets:
```bash
npm install
npm run build
```

4. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure database in `.env`:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=supportflow
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. Run migrations and seeders:
```bash
php artisan migrate
php artisan db:seed
```

7. Create storage link:
```bash
php artisan storage:link
```

8. Configure email settings in `.env`:
```
MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=support@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## System Architecture

### User Roles
1. **Client**
   - Can create and track support tickets
   - Access knowledge base
   - Update profile and company information

2. **Support Agent**
   - Handle assigned tickets
   - Create knowledge base articles
   - Track performance metrics

3. **Administrator**
   - Full system access
   - User management
   - System configuration
   - Performance monitoring

### Key Components

1. **Ticket Management**
   - Priority levels
   - Status tracking
   - SLA monitoring
   - File attachments
   - Comment system

2. **Knowledge Base**
   - Categorized articles
   - Search functionality
   - Version control
   - Access control

3. **User Management**
   - Role-based access control
   - Profile management
   - Activity logging
   - Status tracking

4. **Notification System**
   - Email notifications
   - In-app notifications
   - Custom notification preferences

5. **Reporting System**
   - SLA performance reports
   - Agent performance metrics
   - Ticket analytics
   - Response time tracking

## Database Structure

### Core Tables
- users
- tickets
- ticket_comments
- categories
- articles
- notifications
- settings
- attachments
- feedback

### Relationship Tables
- ticket_assignments
- article_categories
- user_notifications

## Security Features

- Role-based access control
- Input validation
- XSS protection
- CSRF protection
- Password hashing
- Session management
- Rate limiting
- File upload validation

## Maintenance

### Regular Tasks
1. Database backup
2. Log rotation
3. Cache clearing
4. Storage cleanup
5. Performance monitoring

### Commands
```bash
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Optimize application
php artisan optimize

# Run scheduled tasks
php artisan schedule:run
```

## Default Credentials

### Administrator
- Email: admin@example.com
- Password: password

### Test Agent
- Email: john@supportflow.com
- Password: password

### Test Client
- Email: client1@example.com
- Password: password

## Support and Updates

For support and system updates, please contact:
- Email: support@supportflow.com
- Documentation: [documentation-url]
- Issue Tracker: [issues-url]

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Contributors

- [Your Name]
- [Other Contributors]

## Acknowledgments

- Laravel Framework
- TailwindCSS
- Other open-source packages used in this project

---

© 2024 SupportFlow. All rights reserved.

# SupportFlow Documentation

## Dashboard
- Main overview page for agents and admins
- Shows ticket statistics and recent activities
- Quick access to pending tickets and tasks

## Admin Section
### Settings
- System Notification Preferences
- Feedback Moderation Settings
- SLA Configuration
- User Management

### SLA Performance Report
- View agent performance metrics
- Track response times and resolution rates
- Filter reports by date range and agents

## Agent Portal
- View and manage assigned tickets
- Update ticket status and priority
- Add internal notes and responses
- Track SLA compliance

## User Portal
- Submit new support tickets
- View ticket history and status
- Provide feedback on resolved tickets
- Update personal information

## Notification System
- System notifications for ticket updates
- Alert settings for SLA breaches
- Custom notification preferences
