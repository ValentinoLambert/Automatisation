import { describe, it, expect } from 'vitest'
import router from '../index'

describe('Router', () => {
  it('resolves home route', () => {
    const route = router.resolve('/')
    expect(route.name).toBe('home')
  })

  it('resolves demo route', () => {
    const route = router.resolve('/demo')
    expect(route.name).toBe('demo')
  })
})
