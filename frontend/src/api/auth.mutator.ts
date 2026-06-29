import { cookies } from 'next/headers'

const INTERNAL_BASE = process.env.INTERNAL_API_URL ?? 'http://localhost:8000'

// customFetchMutator adapts fetch responses to the generated API client response shapes.
// The generated functions expect a union type that includes { data, status, headers }.
export const customFetchMutator = async <T>(url: string, options: RequestInit): Promise<T> => {
  const cookieStore = await cookies();
  const token = cookieStore.get('auth_token')?.value?.split('|')[1];

  const headers = new Headers(options.headers);
  headers.set('Accept', 'application/json');
  if (token) {
    headers.set('Authorization', `Bearer ${token}`);
  }

  // Replace the hardcoded base URL baked in by Orval at generation time
  const path = url.replace(/^https?:\/\/[^/]+/, '')
  const resolvedUrl = `${INTERNAL_BASE}${path}`

  const response = await fetch(resolvedUrl, { ...options, headers });
  const data = response.status === 204 ? null : await response.json();

  // Construct a result object that matches the generated response unions (includes headers)
  const result = { data, status: response.status, headers: response.headers } as unknown as T;

  return result;
}
