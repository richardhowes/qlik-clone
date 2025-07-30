<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto py-8 px-4">
      <Card>
        <CardHeader>
          <CardTitle>AI Test with Prism</CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
          <div>
            <Label for="prompt">Enter your prompt</Label>
            <Textarea
              id="prompt"
              v-model="prompt"
              placeholder="Ask me anything..."
              rows="4"
              class="w-full"
            />
          </div>
          <Button @click="generateResponse" :disabled="isLoading || !prompt.trim()">
            {{ isLoading ? 'Generating...' : 'Generate Response' }}
          </Button>
          
          <Alert v-if="error" variant="destructive">
            <AlertDescription>{{ error }}</AlertDescription>
          </Alert>

          <div v-if="response" class="mt-6">
            <Label>AI Response:</Label>
            <div class="mt-2 p-4 bg-muted rounded-lg whitespace-pre-wrap">
              {{ response }}
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import { Alert, AlertDescription } from '@/components/ui/alert'

const prompt = ref('')
const response = ref('')
const error = ref('')
const isLoading = ref(false)

const generateResponse = async () => {
  if (!prompt.value.trim()) return
  
  isLoading.value = true
  error.value = ''
  response.value = ''

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    console.log('CSRF Token:', csrfToken)
    
    const res = await fetch('/ai/generate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken || '',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({ prompt: prompt.value }),
    })

    console.log('Response status:', res.status)
    const text = await res.text()
    console.log('Response text:', text)
    
    let data
    try {
      data = JSON.parse(text)
    } catch (e) {
      console.error('Failed to parse JSON:', e)
      error.value = `Server error: ${text.substring(0, 200)}`
      return
    }
    
    if (data.success) {
      response.value = data.response
    } else {
      error.value = data.error || 'An error occurred while generating the response.'
    }
  } catch (err) {
    error.value = 'Failed to connect to the server. Please try again.'
    console.error('Fetch error:', err)
  } finally {
    isLoading.value = false
  }
}
</script>