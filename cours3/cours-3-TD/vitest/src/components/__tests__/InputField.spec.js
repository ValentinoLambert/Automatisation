import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import InputField from '../InputField.vue'

describe('InputField', () => {
  it('renders properly', () => {
    const wrapper = mount(InputField)
    expect(wrapper.text()).toContain('Text:')
  })

  it('updates text', async () => {
    const wrapper = mount(InputField)
    const input = wrapper.find('input')
    await input.setValue('Hello World')
    expect(wrapper.text()).toContain('You entered: Hello World')
  })
})
