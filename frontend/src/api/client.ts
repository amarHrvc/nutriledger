const BASE_URL =  process.env.NEXT_PUBLIC_API_URL

async function request<T>(path: string, options: RequestInit = {}): Promise<T> {
  const token = typeof window !== 'undefined' ? localStorage.getItem('token') : null

  const response = await fetch(`${BASE_URL}/${path}`, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...options.headers,
    },
  });

  if (!response.ok) throw new Error(`${response.status} ${response.statusText}`);

  return response.json() as Promise<T>;
}

export const  client = {
  get: <T,>(path: string): Promise<T> => request<T>(path, { method: 'GET' }),
  post: <T,>(path: string, body: any): Promise<T> => request<T>(path, { method: 'POST', body: JSON.stringify(body) }),
  put: <T,>(path: string, body: any): Promise<T> => request<T>(path, { method: 'PUT', body: JSON.stringify(body) }),
  delete: <T,>(path: string): Promise<T> => request<T>(path, { method: 'DELETE'}),
}
