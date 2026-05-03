import { defineConfig } from 'orval'

export default defineConfig({
  nutribase: {
    input: {
      target: 'http://localhost:8000/docs/api.json'
    },
    output: {
      target: './src/api/generated',
      client: 'fetch',
      mode: 'tags-split',
      baseUrl: process.env.INTERNAL_API_URL ?? 'http://localhost:8000/api',
      override: {
        //custom wrapper
        mutator: {
          path: './src/api/auth.mutator.ts',
          name: 'customFetchMutator'
        }
      }
    }
  }
})
