'use client'

import Box from '@mui/material/Box'
import { styled } from '@mui/material/styles'
import type { BoxProps } from '@mui/material/Box'
import type { Props } from 'react-apexcharts'

import ReactApexcharts from '@/libs/ApexCharts'

type ApexChartWrapperProps = Props & {
	boxProps?: BoxProps
}

const ApexChartWrapper = styled(Box)<BoxProps>(({ theme }) => ({
	'& .apexcharts-canvas': {
		"& line[stroke='transparent']": { display: 'none' },
		'& .apexcharts-tooltip': {
			boxShadow: 'var(--mui-customShadows-xs)',
			borderColor: 'var(--mui-palette-divider)',
			background: 'var(--mui-palette-background-paper)',
			'& .apexcharts-tooltip-title': {
				fontWeight: 600,
				borderColor: 'var(--mui-palette-divider)',
				background: 'var(--mui-palette-background-paper)',
			},
			'&.apexcharts-theme-light': { color: 'var(--mui-palette-text-primary)' },
		},
		'& .apexcharts-xaxistooltip': {
			borderColor: 'var(--mui-palette-divider)',
			background: 'var(--mui-palette-grey-50)',
			...theme.applyStyles('dark', { background: 'var(--mui-palette-customColors-bodyBg)' }),
		},
		'& .apexcharts-text, & .apexcharts-tooltip-text, & .apexcharts-legend-text': {
			fontFamily: `${theme.typography.fontFamily} !important`,
		},
		'& .apexcharts-marker': { boxShadow: 'none' },
	},
})) as typeof Box

const AppReactApexCharts = ({ boxProps, ...rest }: ApexChartWrapperProps) => (
	<ApexChartWrapper {...boxProps}>
		<ReactApexcharts {...rest} />
	</ApexChartWrapper>
)

export default AppReactApexCharts
