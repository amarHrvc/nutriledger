Laravel AI SDK: Technical Architecture and Implementation Guide

To provide a comprehensive learning base for an AI agent, this index is organized into five distinct "files" in Markdown format. These files aggregate technical implementation details, architectural patterns, and production guardrails found in the sources.
File 1: Core Architecture and Agent Fundamentals
# Laravel AI SDK: Core Architecture

The Laravel AI SDK provides a unified, expressive API for interacting with multiple AI providers (OpenAI, Anthropic, Gemini, Groq, etc.) using a consistent, Laravel-native interface [1, 2].

## 1. Installation & Setup
- **Installation:** `composer require laravel/ai` [2, 3].
- **Publish Assets:** `php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"` [4].
- **Migration:** `php artisan migrate` creates `agent_conversations` and `agent_conversation_messages` tables [5, 6].

## 2. The Agent Class
Agents are the fundamental building blocks, acting as specialized assistants (e.g., Sales Coach, Support Bot) [7].
- **Command:** `php artisan make:agent AgentName` [8].
- **Structure:** Agents encapsulate instructions (system prompt), tools, and output schemas [8, 9].
- **Attributes:** Configure behavior via PHP attributes:
    - `#[Temperature(0.0)]`: Ensures deterministic, factual responses [10, 11].
    - `#[MaxTokens(1024)]`: Limits response length [11, 12].
    - `#[UseCheapestModel]` / `#[UseSmartestModel]`: Automatically routes tasks to appropriate models [11, 13].

## 3. Interaction Methods
- **Prompting:** `$agent->prompt('query')` or `agent('query')` for anonymous agents [8, 9, 11].
- **Conversation Memory:** Use the `RemembersConversations` trait and `forUser($user)` to persist history automatically [14-16].
- **Structured Output:** Implement `HasStructuredOutput` and a `schema()` method for predictable JSON responses [17-19].
--------------------------------------------------------------------------------
File 2: Advanced Multi-Agent Workflow Patterns
# Laravel AI SDK: Multi-Agent Workflows

Based on industry research, the SDK supports five primary patterns for complex tasks [20, 21].

| Pattern | Implementation Detail | Best Use Case |
| :--- | :--- | :--- |
| **Prompt Chaining** | One agent's output is passed to the next via a `Pipeline` [22]. | Sequential tasks (Draft → Review → Refine) [22]. |
| **Routing** | A classifier agent selects the best specialist or model based on complexity [23]. | Variable inputs (Support vs. Billing vs. Technical) [23]. |
| **Parallelization** | Uses `Concurrency::run()` to execute independent agents simultaneously [24]. | Independent analyses (Code review by 3 specialists) [25]. |
| **Orchestrator-Workers** | A lead agent delegates sub-tasks to worker agents defined as tools [25]. | Dynamic planning where steps aren't known upfront [26]. |
| **Evaluator-Optimizer** | An iterative loop where one agent evaluates another's output against a bar [27]. | High-quality content (Writing, translation, coding) [21]. |
--------------------------------------------------------------------------------
File 3: Data Intelligence and RAG (Retrieval-Augmented Generation)
# Laravel AI SDK: RAG and Semantic Search

The SDK bridges the gap between AI and private application data using vector embeddings [28, 29].

## 1. Vector Database Setup
- **PostgreSQL + pgvector:** Native support for vector columns [30, 31].
- **Migration:** `table->vector('embedding', 1536)->index()` (1,536 dimensions for OpenAI) [31, 32].
- **Model Casting:** Cast the vector column to an `array` in your Eloquent model [33].

## 2. Ingestion and Search
- **Generating Embeddings:** Use `Str::of($text)->toEmbeddings()` or `Embeddings::for([...])` [31, 34].
- **Vector Search:** Use the `whereVectorSimilarTo` Eloquent method to find semantically close data [33, 35].
- **Reranking:** Improve results using `Reranking::rerank($results)` or the collection macro `->rerank($query)` [36, 37].

## 3. Integrated Tools
- **SimilaritySearch:** A built-in tool allowing agents to query Eloquent models directly [38, 39].
- **FileSearch:** A provider-native tool (OpenAI/Gemini) for searching uploaded documents in vector stores [40-42].
--------------------------------------------------------------------------------
File 4: Production Resilience and Deployment
# Laravel AI SDK: Production Implementation

AI requests are slower and more prone to failure. Use these guardrails in production [43].

## 1. Resilience Mechanisms
- **Failover:** Pass an array of providers/models; the SDK switches automatically on rate limits or outages [40, 44-46].
- **Queueing:** Use `$agent->queue()` to process AI tasks in the background [14, 47, 48].
- **Timeouts:** Configure layers (#[Timeout(120)]) to prevent production infrastructure from killing long-running tasks [49, 50].

## 2. Real-Time Delivery
- **Streaming:** Use `$agent->stream()` for Server-Sent Events (SSE) [51-53].
- **Broadcasting:** Use `broadcastOnQueue()` to send streaming tokens to frontend WebSockets (Reverb/Echo) as they generate [51, 54, 55].

## 3. Security and Validation
- **Middleware:** Use `make:agent-middleware` to intercept, log, or sanitize prompts [56-58].
- **Output Validation:** **CRITICAL:** The SDK does not perform server-side validation of AI JSON. Always validate structured output manually using Laravel's Validator [59, 60].
- **Tool Security:** Treat tool inputs as untrusted user input; validate all arguments inside the tool's `handle()` method [61, 62].
--------------------------------------------------------------------------------
File 5: Extensions and Developer Ecosystem
# Laravel AI SDK: Extended Ecosystem

## 1. Laravel Boost
- **Purpose:** A development dependency that gives coding agents (Cursor, Claude Code) context about your specific app [63, 64].
- **Function:** Installs an MCP server that exposes routes, schema, config, and logs to the AI [63, 64].

## 2. Laravel MCP (Model Context Protocol)
- **Purpose:** Exposes your application as a "server" for external AI clients to use as tools [5, 65].
- **Secured:** Integrated with Laravel middleware, Sanctum, and OAuth for safe data access [65].

## 3. AI SDK Skills
- **Modular Instructions:** Define reusable capabilities in `SKILL.md` files [66].
- **Progressive Disclosure:** Agents see only skill descriptions and "load" full instructions only when needed, saving tokens [67, 68].

## 4. Testing API
- Use `Agent::fake()`, `Image::fake()`, and `Audio::fake()` to mock responses [69-72].
- Perform assertions such as `Agent::assertPrompted(...)` to ensure correct application logic without burning API credits [70, 73].

----------------


Laravel AI SDK: Architecture and Multi-Agent Orchestration Guide

To create a solid foundation for an AI agent, this index is organized into five Markdown-formatted files. Each section includes a citation followed by a summary of the resource it references to provide clear context for the agent.
--------------------------------------------------------------------------------
File 1: Core Architecture and Agent Fundamentals
The Laravel AI SDK provides a unified, expressive API for interacting with various AI providers through a consistent, Laravel-friendly interfaceIntroduction to the unified SDK API].
1. The Agent Class Agents are the fundamental building blocks, acting as specialized assistants like a sales coach or support botAgents as specialized PHP classes].
   Generation: Create agents using php artisan make:agent AgentNameArtisan command for agent creation].
   Prompting: Interact using the prompt() method or the anonymous agent() helper for quick tasksMethods for initiating AI interactions].
2. Configuration Attributes Behavior is defined via PHP attributes directly in the agent classAttributes for fine-tuning agents]:
   #[Temperature(0.0)]: Ensures factual, consistent responsesDeterministic sampling].
   #[MaxTokens(1024)]: Controls the length of the generated outputToken limits].
   #[UseCheapestModel]: Automatically selects the most cost-effective model for a providerCost optimization].
3. Memory and Middleware
   Conversation Memory: Use the RemembersConversations trait to automatically persist history in database tablesAutomated database-backed persistence].
   Middleware: Intercept and modify prompts before they reach the providerMiddleware for logging and security].
--------------------------------------------------------------------------------
File 2: Advanced Multi-Agent Workflow Patterns
Complex tasks are handled through five high-level patterns derived from production researchRationale for multi-agent workflows].
Prompt Chaining: A sequential assembly line where one agent's output is passed to the next via a PipelineOrdered steps pattern].
Routing: A classifier agent directs the input to the most appropriate specialist agent or modelClassification and routing logic].
Parallelization: Independent tasks are executed simultaneously using Concurrency::run()Concurrent execution pattern].
Orchestrator-Workers: A lead agent dynamically plans and delegates sub-tasks to worker agentsDynamic delegation pattern].
Evaluator-Optimizer: An iterative loop where one agent generates and another critiques until a quality bar is metIterative refinement loop].
--------------------------------------------------------------------------------
File 3: Retrieval-Augmented Generation (RAG)
RAG allows agents to answer questions using your private documents rather than just pre-trained dataCore concept of RAG].
1. Embeddings and Semantic Search
   Embeddings: Turning text into numerical coordinates (vectors) that represent meaningDefinition of vectors and embeddings].
   Semantic Matching: Unlike keyword search, this matches the intent of a query by finding the "closest" vectors in spaceMechanism of semantic retrieval].
   Chunks: Large documents must be split into smaller "chunks" to maintain high search relevanceImportance of document segmentation].
2. Database Implementation
   PostgreSQL/pgvector: Native support for storing and searching vector dataDatabase requirements and vector column setup].
   Vector Search: Use whereVectorSimilarTo() to filter Eloquent models by semantic proximityEloquent methods for vector search].
   SimilaritySearch Tool: A built-in agent tool that allows models to automatically query your database for contextTool for automated RAG retrieval].
--------------------------------------------------------------------------------
File 4: Production Resilience and Reliability
AI operations are slow and prone to transient failures, requiring specific production guardrailsChallenges of production AI].
1. Queues and Scaling
   Queuing: Use $agent->queue() to process slow AI tasks in the backgroundQueued execution for resilience].
   Dedicated AI Queues: It is critical to isolate AI workloads from core application jobs to prevent cascading failures during provider outagesStrategy for dedicated queue workers].
2. Failover and Reliability
   Automatic Failover: Provide an array of providers; the SDK automatically switches if the primary provider hits rate limits or goes downFailover mechanism and exception handling].
   Streaming: Deliver tokens to the frontend in real-time via Server-Sent Events (SSE)Real-time token delivery].
3. Security and Validation
   Server-Side Validation: CRITICAL: The SDK does not perform server-side validation of structured JSON output. Developers must manually validate AI data using Laravel's ValidatorMandatory security validation warning].
   Tool Safety: Always validate tool arguments inside the handle() method to prevent prompt injectionSecurity practices for tool execution].
--------------------------------------------------------------------------------
File 5: The Extended Ecosystem (Boost, MCP, and Skills)
Beyond basic features, the ecosystem offers tools for development and extensibility.
Laravel Boost: A development tool that provides coding agents (like Cursor) with context about your app's routes, logs, and database schemaContext-aware coding assistant].
Laravel MCP (Model Context Protocol): A standard for exposing your application's data and functions as tools that external AI clients can securely callExtending app capabilities to external AIs].
AI Skills: Modular, reusable capability files (SKILL.md) that agents can "discover" and load only when needed, which prevents context window bloatModular instruction system and progressive disclosure].
Reranking: Improving search accuracy by running a second pass over initial results to determine true relevancePost-search relevance optimization].
