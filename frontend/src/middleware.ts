import { NextResponse } from 'next/server'
import type { NextRequest } from 'next/server'

export function middleware(request: NextRequest) {
  const { pathname } = request.nextUrl
  const token = request.cookies.get('auth_token')?.value

  const isProtected = pathname.startsWith('/dashboard')
  const isLoginPage = pathname === '/login'

  if (isProtected && !token) {
    const loginUrl = new URL('/login', request.url)

    loginUrl.searchParams.set('callbackUrl', pathname)

    return NextResponse.redirect(loginUrl)
  }

  if (isLoginPage && token) {
    const callbackUrl = request.nextUrl.searchParams.get('callbackUrl')
    const destination = callbackUrl?.startsWith('/dashboard') ? callbackUrl : '/dashboard/home'

    return NextResponse.redirect(new URL(destination, request.url))
  }

  return NextResponse.next()
}

export const config = {
  matcher: ['/((?!_next/static|_next/image|api|images|assets|favicon\\.ico).*)'],
}
