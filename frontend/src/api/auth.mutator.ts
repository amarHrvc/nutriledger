import { cookies } from 'next/headers'

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

  const response = await fetch(url, { ...options, headers });
  const data = response.status === 204 ? null : await response.json();

  // Construct a result object that matches the generated response unions (includes headers)
  const result = { data, status: response.status, headers: response.headers } as unknown as T;

  return result;
}
