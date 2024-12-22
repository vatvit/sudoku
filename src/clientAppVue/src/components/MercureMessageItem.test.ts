import { mount } from '@vue/test-utils'
import MercureMessageItem from './MercureMessageItem.vue'

test('mount component', async () => {
    expect(MercureMessageItem).toBeTruthy()

    const wrapper = mount(MercureMessageItem, {
        props: {
            id: 'someID',
            data: {
                key: 'someKey',
            }
        }
    })

    expect(wrapper.text()).toContain('ID: someID')
    expect(wrapper.text()).toContain('"key": "someKey"')
    expect(wrapper.text()).toMatchSnapshot()
})