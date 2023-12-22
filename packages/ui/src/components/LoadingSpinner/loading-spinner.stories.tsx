import { ComponentStory, ComponentMeta } from '@storybook/react'
import LoadingSpinner from './index'

export default {
  title: 'Components/LoadingSpinner',
  component: LoadingSpinner,
} as ComponentMeta<typeof LoadingSpinner>

const Template: ComponentStory<typeof LoadingSpinner> = (args) => <LoadingSpinner {...args} />

export const AnimatedSpinner: ComponentStory<typeof LoadingSpinner> = Template.bind({})
