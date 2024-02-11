import { mount } from '@vue/test-utils'
import MercureSubscribe from './MercureSubscribe.vue'
// @ts-ignore
import { sources, EventSource } from 'eventsourcemock'
import * as event from '../modules/event';

const buildEventSourceSpy = vi.spyOn(event, 'buildEventSource')
buildEventSourceSpy.mockReturnValue(EventSource)

const messageEvent = new MessageEvent('foo', {
    data: '1',
});

const mercurePublicUrl = 'http://testMercurePublicUrl.example.com'

test('mount component', async () => {

    expect(MercureSubscribe).toBeTruthy()

    const wrapper = mount(MercureSubscribe, {
        global: {
            provide: {
                $sudoku: {config: {mercurePublicUrl: mercurePublicUrl}}
            }
        }
    })

    console.log(wrapper.text());

    sources[mercurePublicUrl].emit(
        messageEvent.type,
        messageEvent
    );

    expect(wrapper.text()).toContain('Subscribed topic: no subscription')
    expect(wrapper.text()).toMatchSnapshot()
})