import { describe, it, expect, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import CounterComponent from '../CounterComponent.vue'

describe('CounterComponent', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('renders properly', () => {
    const wrapper = mount(CounterComponent)
    expect(wrapper.text()).toContain('Counter: 0')
  })

  it('increments', async () => {
    const wrapper = mount(CounterComponent)
    const buttons = wrapper.findAll('button')
    await buttons[1].trigger('click') 
    expect(wrapper.text()).toContain('Counter: 1')
  })

  it('decrements', async () => {
    const wrapper = mount(CounterComponent)
    const buttons = wrapper.findAll('button')
    await buttons[0].trigger('click') 
    expect(wrapper.text()).toContain('Counter: -1')
  })
})
