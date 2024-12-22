import { mount } from '@vue/test-utils'
import MercureSubscribe from './MercureSubscribe.vue'
// @ts-ignore
import * as eventsourcemock from 'eventsourcemock'
import * as event from '../modules/event'

const EventSource = eventsourcemock.default
const sources = eventsourcemock.sources

const buildEventSourceSpy = vi.spyOn(event, 'buildEventSource')

const messageEvent = new MessageEvent('foo', {
    data: '1',
});

const mercurePublicUrl = 'http://testMercurePublicUrl.example.com'

test('mount component', async () => {

    expect(MercureSubscribe).toBeTruthy()

    buildEventSourceSpy.mockReturnValue(new EventSource(mercurePublicUrl))

    const wrapper = mount(MercureSubscribe, {
        global: {
            provide: {
                $sudoku: {config: {mercurePublicUrl: mercurePublicUrl}}
            }
        }
    })

    sources[mercurePublicUrl].emit(
        messageEvent.type,
        messageEvent
    );

    expect(wrapper.text()).toContain('Subscribed topic: no subscription')
    expect(wrapper.text()).toMatchSnapshot()
})
