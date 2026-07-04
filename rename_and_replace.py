import os
import re

replacements = {
    r'\bTucuy\b': 'Abertamente',
    r'\btucuy\b': 'abertamente',
    r'\bTUCUY\b': 'ABERTAMENTE',
    r'\bC#\b': 'PHP',
    r'\bcsharp\b': 'php',
    r'\bCSharp\b': 'Php',
    r'\b\.NET Core\b': 'Laravel',
    r'\b\.NET\b': 'Laravel',
    r'\bdotnet\b': 'laravel',
    r'\bASP\.NET Core\b': 'Laravel',
    r'\bASP\.NET\b': 'Laravel',
    r'\bEntity Framework Core\b': 'Eloquent',
    r'\bEntity Framework\b': 'Eloquent',
    r'\bEF Core\b': 'Eloquent',
    r'\bEF\b': 'Eloquent',
    r'\bDbContext\b': 'Eloquent Model',
    r'\bxUnit\b': 'Pest',
    r'\bMoq\b': 'Mockery',
    r'\bLINQ\b': 'Collections/Query Builder',
    r'\bappsettings\.json\b': '.env',
    r'\bNuget\b': 'Composer',
    r'\bProgram\.cs\b': 'routes/api.php',
    r'\bStartup\.cs\b': 'AppServiceProvider.php',
    r'\bIActionResult\b': 'JsonResponse',
    r'\bTask<\b': '', # Removes Task<...
    r'\bSaveChangesAsync\b': 'save',
    r'\bToListAsync\b': 'get',
    r'\bFirstOrDefaultAsync\b': 'first',
    r'\bAsNoTracking\b': 'toBase', # kind of Eloquent equivalent
    r'\bdotnet build\b': 'composer install',
    r'\bdotnet test\b': 'php artisan test',
    r'\bNuGet\b': 'Composer',
    r'\bnuget\b': 'composer',
}

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    new_content = content
    for pattern, replacement in replacements.items():
        new_content = re.sub(pattern, replacement, new_content, flags=re.IGNORECASE if 'tucuy' in pattern.lower() else 0)

    # Hard replacements without regex boundary for some specific phrases if needed
    # But regex is better to avoid breaking URLs etc.
    new_content = new_content.replace('csharp-ef-conventions', 'php-eloquent-conventions')
    new_content = new_content.replace('dotnet-reviewer', 'laravel-reviewer')
    new_content = new_content.replace('ef-query-optimizer', 'eloquent-query-optimizer')

    if content != new_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated: {filepath}")

def main():
    base_dir = '.agents'
    # File renames
    renames = [
        ('rules/csharp-ef-conventions.md', 'rules/php-eloquent-conventions.md'),
        ('rules/dotnet-reviewer.md', 'rules/laravel-reviewer.md'),
        ('skills/ef-query-optimizer', 'skills/eloquent-query-optimizer'),
        ('workflows/rodar-testes.md', 'workflows/rodar-testes.md') # just content change
    ]

    for root, dirs, files in os.walk(base_dir):
        for filename in files:
            filepath = os.path.join(root, filename)
            process_file(filepath)

    # Rename files/folders
    for old_rel, new_rel in renames:
        old_path = os.path.join(base_dir, old_rel)
        new_path = os.path.join(base_dir, new_rel)
        if os.path.exists(old_path):
            os.rename(old_path, new_path)
            print(f"Renamed: {old_path} -> {new_path}")

if __name__ == "__main__":
    main()
