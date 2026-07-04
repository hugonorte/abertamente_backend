import os
import re

replacements = {
    r'C#': 'PHP',
    r'c#': 'php',
    r'\bASPLaravel\b': 'Laravel',
    r'\bASP\.NET\b': 'Laravel',
    r'\bApi_Tucuy\.Test\b': 'tests',
    r'\bApi_Tucuy\b': 'abertamente',
    r'\bTucuyQqahuacDevelopment\b': 'abertamente-backend',
    r'\bTucuyQqahuacFrontend\b': 'abertamente-frontend',
    r'\.NET\b': 'Laravel',
    r'\.net\b': 'laravel',
    r'\bdotnet\b': 'php artisan',
    r'\blaravel-ef migrations add\b': 'php artisan make:migration',
    r'\blaravel-ef database update\b': 'php artisan migrate',
    r'\blaravel test\b': 'php artisan test',
    r'TucuyQqahuacDevelopment\.sln': 'composer.json',
    r'AppDbContext\.cs': 'DatabaseSeeder.php',
    r'\bnamespace\b.*Api_Tucuy\.Models': r'namespace App\\Models',
}

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    new_content = content
    for pattern, replacement in replacements.items():
        new_content = re.sub(pattern, replacement, new_content)

    if content != new_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated: {filepath}")

def main():
    base_dir = '.agents'
    for root, dirs, files in os.walk(base_dir):
        for filename in files:
            filepath = os.path.join(root, filename)
            process_file(filepath)

if __name__ == "__main__":
    main()
