export function useAuth() {
  const cookieOptions = {
    maxAge: 60 * 60 * 24 * 7,
    path: '/',
    sameSite: 'lax'
  }

  const token = useCookie('token', cookieOptions)
  const userCookie = useCookie('user', cookieOptions)
  const roleCookie = useCookie('role', cookieOptions)

  const parseUser = (raw) => {
    if (!raw) return null
    if (typeof raw === 'object') return raw
    try {
      const parsed = JSON.parse(raw)
      return typeof parsed === 'string' ? JSON.parse(parsed) : parsed
    } catch {
      return null
    }
  }

  const userState = useState('auth_user', () => parseUser(userCookie.value))

  const user = computed(() => {
    if (userState.value) return userState.value
    return parseUser(userCookie.value)
  })

  const role = computed(() => {
    let r = roleCookie.value || user.value?.role
    if (!r && import.meta.client) {
      r = localStorage.getItem('user_role')
    }
    return String(r || 'kasir').toLowerCase().trim()
  })

  const isAdmin = computed(() => {
    return role.value === 'admin'
  })

  const isKasir = computed(() => {
    return role.value === 'kasir'
  })

  const isAuthenticated = computed(() => {
    return Boolean(token.value)
  })

  const setAuth = (userData, tokenValue) => {
    const rawRole = userData?.role || 'kasir'
    const cleanRole = String(rawRole).toLowerCase().trim()

    token.value = tokenValue
    userCookie.value = JSON.stringify(userData)
    roleCookie.value = cleanRole
    userState.value = userData

    if (import.meta.client) {
      localStorage.setItem('user_role', cleanRole)
      localStorage.setItem('user_data', JSON.stringify(userData))
      localStorage.setItem('auth_token', tokenValue)
    }
  }

  const clearAuth = () => {
    token.value = null
    userCookie.value = null
    roleCookie.value = null
    userState.value = null

    if (import.meta.client) {
      localStorage.removeItem('user_role')
      localStorage.removeItem('user_data')
      localStorage.removeItem('auth_token')
    }
  }

  return {
    user,
    token,
    role,
    isAdmin,
    isKasir,
    isAuthenticated,
    setAuth,
    clearAuth,
    logout: clearAuth
  }
}
