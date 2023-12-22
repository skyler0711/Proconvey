import { ComponentStory, ComponentMeta } from '@storybook/react'
import Switch from './index'

export default {
  title: 'Components/Switch',
  component: Switch,
} as ComponentMeta<typeof Switch>

const Template: ComponentStory<typeof Switch> = (args) => <Switch {...args} />

export const Default: ComponentStory<typeof Switch> = Template.bind({})
Default.args = {
  label: 'Toggle',
}

export const WithError: ComponentStory<typeof Switch> = Template.bind({})
WithError.args = {
  label: 'Toggle',
  error: 'This is an error',
}
