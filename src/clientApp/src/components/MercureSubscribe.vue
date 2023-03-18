<script>
  import './MercureMessageItem.vue';
  import MercureMessageItem from "@/components/MercureMessageItem.vue";

  let mercureSubscription;

  export default {
    props: {
      messages: {
        type: Array,
        default: [],
      },
      subscribedTopic: String,
    },
    data() {
      return {
        messages: [],
        subscribedTopic: '',
      };
    },
    components: {
      MercureMessageItem
    },
    mounted() {
      this.subscribe();
    },
    methods: {
      subscribe() {
        this.subscribedTopic = this.$refs.subscriptionTopicName.value;

        const url = new URL(window.sudoku.config.mercurePublicUrl);
        url.searchParams.append('topic', this.subscribedTopic);

        if (mercureSubscription) {
          mercureSubscription.close();
        }
        mercureSubscription = new EventSource(url);
        mercureSubscription.onmessage = this.onMessage;
      },
      unsubscribe() {
        this.subscribedTopic = '';
        mercureSubscription.close();
        mercureSubscription = undefined;
      },
      clearLog() {
        this.messages = [];
      },
      onMessage(message) {
        console.log(message);
        this.messages.unshift({
          id: message.lastEventId,
          data: message.data,
        });
        console.log(this.messages);
      },
    },
  }
</script>

<template>
  <div id="status">Subscribed topic: <b>{{ subscribedTopic || 'no subscription' }}</b></div>
  <input id="subscriptionTopicName" ref="subscriptionTopicName" name="topic" placeholder="topic name e.g. 'my-private-topic'" value="*">
  <button id="subscribe" @click="subscribe">Subscribe</button>
  <button id="unsubscribe" @click="unsubscribe">Unsubscribe</button>
  <button id="clear" @click="clearLog">Clear log</button>
  <div id="messages" style="border: 1px red; padding: 5px;">
    Messages:
    <MercureMessageItem v-for="message in messages" :key="message.id" :id="message.id" :data="message.data" />
  </div>
</template>
