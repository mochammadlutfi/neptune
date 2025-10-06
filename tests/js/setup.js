import { vi } from 'vitest'

// Mock global objects
global.window = {
  location: {
    href: 'http://localhost:3000',
    origin: 'http://localhost:3000'
  },
  config: {
    app_name: 'NEPTUNE DVR',
    app_url: 'http://localhost:3000',
    api_url: 'http://localhost:3000/api'
  }
}

// Mock navigator
global.navigator = {
  userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
}

// Mock document methods
global.document = {
  querySelector: vi.fn(),
  createElement: vi.fn(() => ({
    setAttribute: vi.fn(),
    getAttribute: vi.fn(),
    content: ''
  })),
  head: {
    appendChild: vi.fn()
  },
  cookie: ''
}

// Mock axios
vi.mock('axios', () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
    create: vi.fn(() => ({
      get: vi.fn(),
      post: vi.fn(),
      put: vi.fn(),
      delete: vi.fn(),
      interceptors: {
        request: { use: vi.fn() },
        response: { use: vi.fn() }
      }
    })),
    interceptors: {
      request: { use: vi.fn() },
      response: { use: vi.fn() }
    }
  },
  get: vi.fn(),
  post: vi.fn(),
  put: vi.fn(),
  delete: vi.fn()
}))

// Mock localStorage
const localStorageMock = {
  getItem: vi.fn(),
  setItem: vi.fn(),
  removeItem: vi.fn(),
  clear: vi.fn()
}
global.localStorage = localStorageMock

// Mock sessionStorage
const sessionStorageMock = {
  getItem: vi.fn(),
  setItem: vi.fn(),
  removeItem: vi.fn(),
  clear: vi.fn()
}
global.sessionStorage = sessionStorageMock