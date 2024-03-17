<script setup lang="ts">
import { ref, onMounted, inject } from 'vue'
import MercureMessageItem from "./MercureMessageItem.vue"
import * as event from './../modules/event/index.ts'

type Sudoku = {config: {mercurePublicUrl: string}};
const config = inject<Sudoku>('$sudoku') as Sudoku

let mercureSubscription: any

const messages = ref<{id: number; data: object}[]>([])
const subscribedTopic = ref('')

const subscriptionTopicName = ref('')

onMounted(() => {
  subscribe()
})

function subscribe() {
  subscribedTopic.value = subscriptionTopicName.value;

  const url = new URL(config.config.mercurePublicUrl);
  url.searchParams.append('topic', subscribedTopic.value);

  if (mercureSubscription) {
    mercureSubscription.close();
  }
  mercureSubscription = event.buildEventSource(url.toString());
  mercureSubscription.onmessage = onMessage;
}
function unsubscribe() {
  subscribedTopic.value = '';
  mercureSubscription.close();
  mercureSubscription = undefined;
}
function clearLog() {
  messages.value = [];
}
function onMessage(message: {lastEventId: number; data: object}) {
  console.log(message);
  messages.value.unshift({
    id: message.lastEventId,
    data: message.data,
  });
}
</script>

<template>
  <div id="status">Subscribed topic: <b>{{ subscribedTopic || 'no subscription' }}</b></div>
  <input id="subscriptionTopicName" v-model="subscriptionTopicName" name="topic" placeholder="topic name e.g. 'my-private-topic'" value="*">
  <button id="subscribe" @click="subscribe">Subscribe</button>
  <button id="unsubscribe" @click="unsubscribe">Unsubscribe</button>
  <button id="clear" @click="clearLog">Clear log</button>
  <div id="messages" style="border: 1px red; padding: 5px;">
    Messages:
    <MercureMessageItem v-for="message in messages" :key="message.id" :id="message.id" :data="message.data" />
  </div>
</template>
