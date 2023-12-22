import { ComponentStory, ComponentMeta } from '@storybook/react'
import StepSidebar from './index'

export default {
  title: 'Components/StepSidebar',
  component: StepSidebar,
} as ComponentMeta<typeof StepSidebar>

export const Expanded: ComponentStory<typeof StepSidebar> = () => {
  return (
    <div className="h-screen flex">
      <StepSidebar currentStep={3}>
        <StepSidebar.Item>Profile</StepSidebar.Item>
        <StepSidebar.Item>Business</StepSidebar.Item>
        <StepSidebar.Item>Team Members</StepSidebar.Item>
        <StepSidebar.Item>Onboarding Letters</StepSidebar.Item>
      </StepSidebar>
    </div>
  )
}
