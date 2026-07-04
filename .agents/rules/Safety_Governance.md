# Safety & Governance

### Data Deletion Policy
- **Mandatory Approval**: Any action involving the deletion of data (databases, tables, or bulk records) in any environment (including Development) must never be performed autonomously.
- **Protocol**: The agent MUST ask for explicit permission from the developer/user and only proceed upon receiving clear authorization.

### Code Structural Integrity
- **Structural Preservation**: All edits must strictly preserve the integrity of control structures, code blocks (braces `{ }`), semicolons, and original indentation.
- **Edit Verification**: The agent must perform a thorough visual review of the replacement block (Replacement Chunk) BEFORE applying it, ensuring the replacement content is a syntactically complete and valid substitute for the target content.
- **Syntax Validation**: Whenever the environment allows (appropriate permissions and active containers), the agent must validate the change syntax (e.g., by running `laravel build`) before marking the task as complete.

### Security & Sensitivity
- **Zero-Secret Policy**: Credentials must never be written in source code. Use `.env` or secure secret managers.
- **Sanitization**: Logs must never capture sensitive user information (PII).
- **Mock Data**: Use only fake/mock data for tests. Never use real production data in development environments.
